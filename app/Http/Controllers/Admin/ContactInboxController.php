<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\ContactReplyMail;
use App\Models\ContactReply;
use App\Models\ContactSubmission;
use App\Services\InboundContactFetcher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ContactInboxController extends Controller
{
    /**
     * Display the contact inbox page.
     */
    public function index(): View
    {
        return view('admin.contact-inbox.index');
    }

    /**
     * Return contact submission data (JSON) — search, filter, pagination.
     */
    public function data(Request $request): JsonResponse
    {
        $query = ContactSubmission::query();

        if ($search = $request->string('search')->trim()->toString()) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($status = $request->string('status')->trim()->toString()) {
            $query->where('status', $status);
        }

        $perPage = min((int) $request->input('per_page', 10), 50);

        $paginator = $query
            ->withCount('replies')
            ->orderByDesc('created_at')
            ->paginate($perPage, [
                'id', 'name', 'email', 'phone', 'subject', 'request_type',
                'message', 'attachment', 'status', 'ip_address', 'created_at',
            ]);

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

    /**
     * Current new/total submission counts.
     */
    private function counts(): array
    {
        return [
            'new' => ContactSubmission::where('status', 'new')->count(),
            'total' => ContactSubmission::count(),
        ];
    }

    /**
     * Return a single submission with its full chat thread.
     */
    public function show(ContactSubmission $contactSubmission): JsonResponse
    {
        $contactSubmission->load('replies');

        return response()->json([
            'submission' => $contactSubmission->only([
                'id', 'name', 'email', 'phone', 'subject', 'request_type',
                'message', 'attachment', 'status', 'ip_address', 'created_at',
            ]),
            'has_attachment' => $contactSubmission->attachmentPath() !== null,
            'replies' => $contactSubmission->replies->map(fn (ContactReply $reply) => [
                'id' => $reply->id,
                'sender' => $reply->sender,
                'body' => $reply->body,
                'attachment' => $reply->attachment,
                'source' => $reply->source,
                'created_at' => $reply->created_at->toIso8601String(),
            ]),
        ]);
    }

    /**
     * Pull inbound e-mail replies right now (throttled ~15s) and return counts.
     *
     * The inbox page calls this every 30 seconds while it is open, so replies
     * that arrive by e-mail show up in the dashboard almost immediately —
     * no background job or cron is needed.
     */
    public function sync(): JsonResponse
    {
        $lock = Cache::lock('contact:inbound-fetch-lock', 30);

        $processed = 0;
        $matched = 0;

        if ($lock->get()) {
            try {
                $last = Cache::get('contact:inbox-page-last-sync');

                if (! $last || now()->getTimestamp() - (int) $last >= 15) {
                    $result = app(InboundContactFetcher::class)->run();
                    $processed = $result['processed'];
                    $matched = $result['matched'];

                    Cache::put('contact:inbox-page-last-sync', now()->getTimestamp(), now()->addMinutes(10));
                }
            } catch (\Throwable $e) {
                Log::warning('[contact-inbox] inbox sync failed: '.$e->getMessage());
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

    /**
     * Store an admin reply, e-mail it to the customer and mark as replied.
     */
    public function reply(Request $request, ContactSubmission $contactSubmission): JsonResponse
    {
        $request->validate([
            'body' => ['required', 'string', 'min:1', 'max:5000'],
        ]);

        $reply = ContactReply::create([
            'contact_submission_id' => $contactSubmission->id,
            'sender' => 'admin',
            'body' => $request->string('body')->toString(),
            'source' => 'dashboard',
        ]);

        if ($contactSubmission->status === 'new') {
            $contactSubmission->update(['status' => 'replied']);
        }

        Mail::to($contactSubmission->email)
            ->send(new ContactReplyMail(
                $contactSubmission,
                $reply->body,
                Auth::user()->name,
            ));

        return response()->json([
            'message' => 'Antwoord verzonden.',
            'reply' => [
                'id' => $reply->id,
                'sender' => 'admin',
                'body' => $reply->body,
                'attachment' => null,
                'source' => 'dashboard',
                'created_at' => $reply->created_at->toIso8601String(),
            ],
            'status' => $contactSubmission->fresh()->status,
        ]);
    }

    /**
     * Update the status of a submission.
     */
    public function status(Request $request, ContactSubmission $contactSubmission): JsonResponse
    {
        $request->validate([
            'status' => ['required', 'in:new,in_progress,replied,closed'],
        ]);

        $contactSubmission->update(['status' => $request->string('status')->toString()]);

        return response()->json([
            'message' => 'Status bijgewerkt.',
            'status' => $contactSubmission->status,
        ]);
    }

    /**
     * Delete a submission (replies cascade, attachments removed).
     */
    public function destroy(ContactSubmission $contactSubmission): JsonResponse
    {
        Storage::disk('local')->deleteDirectory('contact/'.$contactSubmission->id);

        $contactSubmission->delete();

        return response()->json([
            'message' => 'Aanvraag succesvol verwijderd.',
        ]);
    }

    /**
     * Stream the submission attachment for download.
     */
    public function attachment(ContactSubmission $contactSubmission): StreamedResponse
    {
        $path = $contactSubmission->attachmentPath();

        abort_unless($path, 404);

        return Storage::disk('local')->download(
            'contact/'.$contactSubmission->id.'/'.$contactSubmission->attachment,
            Str::afterLast($contactSubmission->attachment, '/'),
        );
    }

    /**
     * Current number of new (unseen) submissions — used for the sidebar badge.
     */
    public function newCount(): JsonResponse
    {
        return response()->json([
            'count' => ContactSubmission::where('status', 'new')->count(),
        ]);
    }
}