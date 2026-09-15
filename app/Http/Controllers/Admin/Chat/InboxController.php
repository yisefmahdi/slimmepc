<?php

namespace App\Http\Controllers\Admin\Chat;

use App\Http\Controllers\Controller;
use App\Mail\ChatReplyMail;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InboxController extends Controller
{
    public function index(): View
    {
        return view('admin.chat.inbox.index');
    }

    public function data(Request $request): JsonResponse
    {
        $query = ChatConversation::query()->with('user:id,name,klantnummer');

        if ($search = $request->string('search')->trim()->toString()) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhereHas('messages', fn ($m) => $m->where('body', 'like', "%{$search}%"));
            });
        }

        if ($status = $request->string('status')->trim()->toString()) {
            $query->where('status', $status);
        }

        $perPage = min((int) $request->input('per_page', 10), 50);

        $paginator = $query
            ->withCount('messages')
            ->withMax('messages', 'created_at')
            ->with('messages')
            ->orderByRaw('COALESCE(messages_max_created_at, created_at) DESC')
            ->orderByDesc('id')
            ->paginate($perPage);

        foreach ($paginator->items() as $row) {
            $last = $row->messages->sortByDesc('created_at')->first();
            $row->last_message = $last ? [
                'body' => $last->body,
                'sender' => $last->sender,
                'created_at' => $last->created_at->toIso8601String(),
            ] : null;
            $row->unread = $row->unreadCount();
            unset($row->messages);
        }

        return response()->json([
            'data' => $paginator->items(),
            'pagination' => [
                'current' => $paginator->currentPage(),
                'last' => $paginator->lastPage(),
                'total' => $paginator->total(),
                'per_page' => $paginator->perPage(),
            ],
            'counts' => $this->counts(),
        ]);
    }

    protected function counts(): array
    {
        return [
            'open' => ChatConversation::whereIn('status', ['ai', 'open'])->count(),
            'handover' => ChatConversation::where('status', 'handed_over')->count(),
            'total' => ChatConversation::count(),
            'unread' => $this->unreadTotal(),
            'avg_rating' => round((float) ChatConversation::whereNotNull('rating')->avg('rating'), 1),
        ];
    }

    protected function unreadTotal(): int
    {
        return (int) DB::table('chat_messages')
            ->join('chat_conversations', 'chat_messages.chat_conversation_id', '=', 'chat_conversations.id')
            ->where('chat_messages.sender', 'customer')
            ->where(function ($q) {
                $q->whereNull('chat_conversations.admin_read_at')
                    ->orWhereColumn('chat_messages.created_at', '>', 'chat_conversations.admin_read_at');
            })
            ->count();
    }

    public function show(ChatConversation $conversation): JsonResponse
    {
        $conversation->load(['messages', 'user:id,name,email,klantnummer']);
        $conversation->update(['admin_read_at' => now()]);

        return response()->json([
            'conversation' => [
                'id' => $conversation->id,
                'name' => $conversation->name,
                'email' => $conversation->email,
                'status' => $conversation->status,
                'ai_enabled' => (bool) $conversation->ai_enabled,
                'rating' => $conversation->rating,
                'rating_comment' => $conversation->rating_comment,
                'rated_at' => $conversation->rated_at?->toIso8601String(),
                'handed_over_at' => $conversation->handed_over_at?->toIso8601String(),
                'created_at' => $conversation->created_at->toIso8601String(),
                'user' => $conversation->user ? [
                    'name' => $conversation->user->name,
                    'klantnummer' => $conversation->user->klantnummer,
                ] : null,
            ],
            'messages' => $conversation->messages->map(fn (ChatMessage $m) => [
                'id' => $m->id,
                'sender' => $m->sender,
                'body' => $m->body,
                'source' => $m->source,
                'photo_url' => $m->attachment ? route('admin.chat.inbox.photo', ['chatMessage' => $m->id]) : null,
                'attachment_name' => $m->attachment ? Str::afterLast($m->attachment, '/') : null,
                'created_at' => $m->created_at->toIso8601String(),
            ]),
        ]);
    }

    /**
     * Admin-antwoord: AI stopt per direct (ai_enabled=false).
     * De klant krijgt het antwoord realtime én per e-mail (tab kan dicht zijn).
     * Gesloten tickets zijn vergrendeld: eerst heropenen via status.
     */
    public function reply(Request $request, ChatConversation $conversation): JsonResponse
    {
        if ($conversation->status === 'closed') {
            return response()->json([
                'message' => 'Ticket gesloten — heropen het gesprek eerst via de status.',
            ], 422);
        }

        $request->validate([
            'body' => ['required_without:attachment', 'nullable', 'string', 'max:5000'],
            'attachment' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,avif', 'max:10240'],
        ], [
            'body.required_without' => 'Typ een antwoord of voeg een foto toe.',
            'attachment.image' => 'Alleen afbeeldingen toegestaan.',
            'attachment.max' => 'De foto mag maximaal 10MB zijn.',
        ]);

        $path = null;
        if ($request->hasFile('attachment') && $request->file('attachment')->isValid()) {
            $file = $request->file('attachment');
            $path = $file->storeAs(
                'chat/'.$conversation->id.'/outbound',
                Str::uuid().'.'.$file->getClientOriginalExtension(),
                'local'
            );
        }

        $reply = $conversation->messages()->create([
            'sender' => 'admin',
            'body' => $request->string('body')->toString() ?: null,
            'attachment' => $path,
            'source' => 'dashboard',
        ]);

        $conversation->update([
            'ai_enabled' => false,
            'status' => $conversation->status === 'ai' ? 'open' : $conversation->status,
        ]);
        $conversation->touchActivity();

        dispatch(function () use ($conversation, $reply) {
            \App\Services\Chat\ChatMailer::send($conversation->email, new ChatReplyMail($conversation->fresh(), $reply->fresh()), 'inbox-reply');
        })->afterResponse();

        return response()->json([
            'message' => 'Antwoord verzonden. AI is uitgeschakeld voor dit gesprek.',
            'reply' => [
                'id' => $reply->id,
                'sender' => 'admin',
                'body' => $reply->body,
                'source' => 'dashboard',
                'photo_url' => $path ? route('admin.chat.inbox.photo', ['chatMessage' => $reply->id]) : null,
                'created_at' => $reply->created_at->toIso8601String(),
            ],
            'ai_enabled' => false,
            'status' => $conversation->fresh()->status,
        ]);
    }

    public function toggleAi(ChatConversation $conversation): JsonResponse
    {
        $conversation->update(['ai_enabled' => ! $conversation->ai_enabled]);

        return response()->json([
            'message' => $conversation->ai_enabled ? 'AI ingeschakeld.' : 'AI uitgeschakeld.',
            'ai_enabled' => (bool) $conversation->ai_enabled,
        ]);
    }

    public function status(Request $request, ChatConversation $conversation): JsonResponse
    {
        $request->validate(['status' => ['required', 'in:ai,open,handed_over,closed']]);
        $wasClosed = $conversation->status === 'closed';
        $conversation->update(['status' => $request->string('status')->toString()]);
        $conversation->touchActivity();

        if ($conversation->status === 'closed' && ! $wasClosed) {
            dispatch(function () use ($conversation) {
                \App\Services\Chat\ChatMailer::send($conversation->email, new \App\Mail\ChatClosedMail($conversation->fresh()), 'close-admin');
            })->afterResponse();
        }

        return response()->json(['message' => 'Status bijgewerkt.', 'status' => $conversation->status]);
    }

    public function photo(ChatMessage $chatMessage): BinaryFileResponse|StreamedResponse
    {
        abort_unless($chatMessage->attachment, 404);
        $path = $chatMessage->attachment;
        abort_unless(Storage::disk('local')->exists($path), 404);

        if (preg_match('/\.(jpe?g|png|webp|avif)$/i', $path)) {
            return response()->file(Storage::disk('local')->path($path), ['Content-Disposition' => 'inline']);
        }

        return Storage::disk('local')->download($path, Str::afterLast($path, '/'));
    }

    public function destroy(ChatConversation $conversation): JsonResponse
    {
        Storage::disk('local')->deleteDirectory('chat/'.$conversation->id);
        $conversation->delete();

        return response()->json(['message' => 'Gesprek verwijderd.']);
    }

    public function newCount(): JsonResponse
    {
        return response()->json([
            'handover' => ChatConversation::where('status', 'handed_over')->count(),
            'unread' => $this->unreadTotal(),
        ]);
    }

    /**
     * Pull inbound e-mail replies right now (throttled ~15s) and return counts.
     * Zelfde patroon als contact-inbox: geen cron nodig.
     */
    public function sync(): JsonResponse
    {
        $lock = Cache::lock('chat:inbox-sync-lock', 30);
        $processed = 0;
        $matched = 0;

        if ($lock->get()) {
            try {
                $last = Cache::get('chat:inbox-page-last-sync');

                if (! $last || now()->getTimestamp() - (int) $last >= 15) {
                    $result = app(\App\Services\InboundContactFetcher::class)->run();
                    $processed = $result['processed'];
                    $matched = $result['matched'];

                    Cache::put('chat:inbox-page-last-sync', now()->getTimestamp(), now()->addMinutes(10));
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('[chat-inbox] inbox sync failed: '.$e->getMessage());
            } finally {
                $lock->release();
            }
        }

        return response()->json([
            'counts' => $this->counts(),
            'processed' => $processed,
            'matched' => $matched,
        ]);
    }
}
