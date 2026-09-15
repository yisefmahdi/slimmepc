<?php

namespace App\Http\Controllers;

use App\Mail\AdminChatNotification;
use App\Mail\ChatOfflineReceived;
use App\Mail\ChatTicketMail;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Services\Ai\Features\ChatAnswerGenerator;
use App\Services\Chat\ChatAvailabilityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ChatController extends Controller
{
    /**
     * Open / gesloten + reden. De widget kiest hiermee chat of offline-formulier.
     */
    public function status(ChatAvailabilityService $service): JsonResponse
    {
        return response()->json($service->status());
    }

    /**
     * Start een gesprek. Eerst het welkomstbericht, daarna pas chatten.
     */
    public function start(Request $request): JsonResponse
    {
        // Principe: het e-mailadres uit de gate is ALTIJD leidend voor alle
        // mails (ook ingelogd) — user_id blijft alleen gekoppeld voor historie.
        // Accountgegevens gelden uitsluitend als fallback bij lege velden.
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'website' => ['prohibited'],
        ], [
            'name.required' => 'Vul je naam in.',
            'email.required' => 'Vul je e-mailadres in.',
            'email.email' => 'Vul een geldig e-mailadres in.',
        ]);

        $user = Auth::user();

        $conversation = ChatConversation::create([
            'guest_token' => Str::random(64),
            'user_id' => $user?->id,
            'name' => $request->string('name')->toString() ?: ($user?->name ?? ''),
            'email' => $request->string('email')->toString() ?: ($user?->email ?? ''),
            'status' => 'ai',
            'last_activity_at' => now(),
            'ip_address' => $request->ip(),
        ]);

        $welcome = 'Hoi '.$conversation->name.'! Ik ben de Slimme-PC assistent. Waar kan ik je mee helpen?';
        $conversation->messages()->create([
            'sender' => 'ai',
            'body' => $welcome,
            'source' => 'ai',
        ]);
        $conversation->touchActivity();

        return response()->json([
            'token' => $conversation->guest_token,
            'conversation_id' => $conversation->id,
            'status' => $conversation->status,
            'messages' => $this->messageList($conversation),
        ], 201);
    }

    /**
     * Bericht + optioneel één foto sturen. AI antwoordt direct (ai/open + ai_enabled).
     */
    public function send(Request $request, ChatAnswerGenerator $generator): JsonResponse
    {
        $request->validate([
            'token' => ['required', 'string', 'size:64'],
            'body' => ['required_without:photo', 'nullable', 'string', 'max:2000'],
            'photo' => ['required_without:body', 'nullable', 'image', 'mimes:jpg,jpeg,png,webp,avif', 'max:10240'],
            'website' => ['prohibited'],
        ], [
            'body.required_without' => 'Typ een bericht of voeg een foto toe.',
            'photo.image' => 'De bijlage moet een afbeelding zijn.',
            'photo.max' => 'De foto mag maximaal 10MB zijn.',
        ]);

        $conversation = $this->resolve($request->string('token')->toString());

        if (in_array($conversation->status, ['closed', 'offline'], true)) {
            return response()->json([
                'message' => 'Dit gesprek is gesloten. Start een nieuw gesprek.',
                'code' => 'conversation_closed',
            ], 422);
        }

        $path = null;
        if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
            $file = $request->file('photo');
            $name = Str::uuid().'.'.$file->getClientOriginalExtension();
            $path = $file->storeAs('chat/'.$conversation->id, $name, 'local');
        }

        $message = $conversation->messages()->create([
            'sender' => 'customer',
            'body' => $request->string('body')->toString() ?: null,
            'attachment' => $path,
            'source' => 'widget',
        ]);

        if ($conversation->status === 'ai') {
            $conversation->update(['status' => 'open']);
        }
        $conversation->touchActivity();

        $reply = null;
        $handoffOffer = false;
        $cards = [];
        $fresh = $conversation->fresh();
        if (in_array($fresh->status, ['ai', 'open'], true) && $fresh->ai_enabled) {
            $result = $generator->generate($fresh, (string) ($message->body ?? 'Foto'), (bool) $path);
            $reply = $conversation->messages()->create([
                'sender' => 'ai',
                'body' => $result['text'],
                'source' => 'ai',
            ]);
            $handoffOffer = $result['handoff_offer'];
            $cards = $result['products'];
            $conversation->touchActivity();
        }

        return response()->json([
            'message' => $this->messageResource($message->fresh(), $conversation),
            'reply' => $reply ? $this->messageResource($reply, $conversation) : null,
            'handoff_offer' => $handoffOffer,
            'products' => $cards,
            'status' => $conversation->fresh()->status,
        ], 201);
    }

    /**
     * Berichten van één gesprek (voor polling + herladen).
     */
    public function messages(Request $request): JsonResponse
    {
        $request->validate(['token' => ['required', 'string', 'size:64']]);

        $conversation = $this->resolve($request->string('token')->toString());

        return response()->json([
            'status' => $conversation->status,
            'ai_enabled' => (bool) $conversation->ai_enabled,
            'messages' => $this->messageList($conversation),
        ]);
    }

    /**
     * Eerdere gesprekken van de ingelogde gebruiker.
     */
    public function history(): JsonResponse
    {
        if (! Auth::check()) {
            return response()->json(['conversations' => []]);
        }

        $items = ChatConversation::where('user_id', Auth::id())
            ->with('messages')
            ->orderByDesc('last_activity_at')
            ->orderByDesc('id')
            ->limit(10)
            ->get()
            ->map(fn (ChatConversation $c) => [
                'id' => $c->id,
                'token' => $c->guest_token,
                'status' => $c->status,
                'rating' => $c->rating,
                'last_message' => $c->messages->last()?->body,
                'updated_at' => $c->last_activity_at?->toIso8601String(),
            ]);

        return response()->json(['conversations' => $items]);
    }

    /**
     * Klant vraagt om een medewerker: AI stopt, notify-mail naar de admin.
     */
    public function handover(Request $request, ChatAnswerGenerator $generator): JsonResponse
    {
        $request->validate(['token' => ['required', 'string', 'size:64']]);

        $conversation = $this->resolve($request->string('token')->toString());

        if ($conversation->status === 'handed_over') {
            return response()->json(['message' => 'Er is al een medewerker onderweg.']);
        }

        if (in_array($conversation->status, ['closed', 'offline'], true)) {
            return response()->json([
                'message' => 'Dit gesprek is gesloten.',
                'code' => 'conversation_closed',
            ], 422);
        }

        $conversation->update(['status' => 'handed_over', 'handed_over_at' => now(), 'ai_enabled' => false]);
        $ticketText = $generator->confirmation($conversation->fresh(), 'ticket');
        $conversation->messages()->create([
            'sender' => 'ai',
            'body' => $ticketText,
            'source' => 'ai',
        ]);
        $conversation->touchActivity();

        $notify = config('contact-inbox.notify_email');
        dispatch(function () use ($conversation, $notify, $ticketText) {
            if ($notify) {
                \App\Services\Chat\ChatMailer::send($notify, new AdminChatNotification($conversation->fresh(), 'handover'), 'handover-admin');
            }
            \App\Services\Chat\ChatMailer::send($conversation->email, new ChatTicketMail($conversation->fresh(), $ticketText), 'handover-customer');
        })->afterResponse();

        return response()->json([
            'message' => 'Een medewerker neemt het gesprek over.',
            'messages' => $this->messageList($conversation->fresh()),
        ]);
    }

    /**
     * Offline-formulier (chat gesloten): opent direct een NORMAAL ticket
     * (handed_over, geen verschil met andere tickets). AI blijft uit.
     */
    public function offline(Request $request, ChatAnswerGenerator $generator): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'message' => ['required', 'string', 'max:2000'],
            'website' => ['prohibited'],
        ], [
            'name.required' => 'Vul je naam in.',
            'email.required' => 'Vul je e-mailadres in.',
            'message.required' => 'Typ je bericht.',
        ]);

        $user = Auth::user();

        $conversation = ChatConversation::create([
            'guest_token' => Str::random(64),
            'user_id' => $user?->id,
            'name' => $request->string('name')->toString(),
            'email' => $request->string('email')->toString(),
            'status' => 'handed_over',
            'ai_enabled' => false,
            'handed_over_at' => now(),
            'last_activity_at' => now(),
            'ip_address' => $request->ip(),
        ]);

        $conversation->messages()->create([
            'sender' => 'customer',
            'body' => $request->string('message')->toString(),
            'source' => 'widget',
        ]);

        // Bedankje EERST genereren (alleen klantbericht in historie),
        // daarna pas het ticketbericht — anders volgt de taal de AI-tekst.
        $thanksText = $generator->confirmation($conversation->fresh(), 'thanks');

        $conversation->messages()->create([
            'sender' => 'ai',
            'body' => $generator->confirmation($conversation->fresh(), 'ticket'),
            'source' => 'ai',
        ]);
        $conversation->touchActivity();

        dispatch(function () use ($conversation) {
            \App\Services\Chat\ChatMailer::send($conversation->email, new ChatOfflineReceived($conversation->fresh()), 'offline-customer');
            if ($notify = config('contact-inbox.notify_email')) {
                \App\Services\Chat\ChatMailer::send($notify, new AdminChatNotification($conversation->fresh(), 'handover'), 'offline-admin');
            }
        })->afterResponse();

        $fresh = $conversation->fresh();

        // Geen token/messages terug: offline blijft e-mail-only,
        // de widget toont alleen het bedankje (geen thread openen).
        return response()->json([
            'message' => 'Ticket aangemaakt.',
            'thanks' => $thanksText,
        ], 201);
    }

    /**
     * Gesprek sluiten (daarna rating-scherm in de widget).
     */
    public function close(Request $request): JsonResponse
    {
        $request->validate(['token' => ['required', 'string', 'size:64']]);

        $conversation = $this->resolve($request->string('token')->toString());
        $wasClosed = $conversation->status === 'closed';
        $conversation->update(['status' => 'closed']);
        $conversation->touchActivity();

        if (! $wasClosed) {
            dispatch(function () use ($conversation) {
                \App\Services\Chat\ChatMailer::send($conversation->email, new \App\Mail\ChatClosedMail($conversation->fresh()), 'close-customer');
            })->afterResponse();
        }

        return response()->json(['message' => 'Gesprek gesloten.']);
    }

    /**
     * Beoordeling na afsluiting (één keer per gesprek).
     */
    public function rate(Request $request): JsonResponse
    {
        $request->validate([
            'token' => ['required', 'string', 'size:64'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ], [
            'rating.required' => 'Kies een aantal sterren.',
        ]);

        $conversation = $this->resolve($request->string('token')->toString());

        if ($conversation->rated_at) {
            return response()->json(['message' => 'Je hebt dit gesprek al beoordeeld.'], 422);
        }

        $conversation->update([
            'rating' => $request->integer('rating'),
            'rating_comment' => $request->string('comment')->toString() ?: null,
            'rated_at' => now(),
        ]);

        return response()->json(['message' => 'Bedankt voor je beoordeling!']);
    }

    /**
     * Eén chatfoto streamen (alleen met geldige token).
     */
    public function photo(Request $request, ChatMessage $message): BinaryFileResponse
    {
        $request->validate(['token' => ['required', 'string', 'size:64']]);

        abort_unless(
            $message->attachment
            && (int) $message->chat_conversation_id === (int) $this->resolve($request->string('token')->toString())->id
            && Storage::disk('local')->exists($message->attachment),
            404
        );

        return response()->file(
            Storage::disk('local')->path($message->attachment),
            ['Content-Disposition' => 'inline']
        );
    }

    protected function resolve(string $token): ChatConversation
    {
        return ChatConversation::where('guest_token', $token)->firstOrFail()->load('messages');
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function messageList(ChatConversation $conversation): array
    {
        return $conversation->messages
            ->take(-100)
            ->map(fn (ChatMessage $m) => $this->messageResource($m, $conversation))
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    protected function messageResource(ChatMessage $message, ChatConversation $conversation): array
    {
        return [
            'id' => $message->id,
            'sender' => $message->sender,
            'body' => $message->body,
            'photo_url' => $message->attachment
                ? route('ai-chat.photo', ['message' => $message->id, 'token' => $conversation->guest_token])
                : null,
            'created_at' => $message->created_at?->toIso8601String(),
        ];
    }
}
