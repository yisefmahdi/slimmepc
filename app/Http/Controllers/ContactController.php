<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactSubmissionRequest;
use App\Mail\AdminContactNotification;
use App\Mail\ContactReceived;
use App\Models\ContactSubmission;
use App\Services\AdminPushNotifier;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ContactController extends Controller
{
    /**
     * Store a new contact submission from the public contact form.
     */
    public function submit(StoreContactSubmissionRequest $request): JsonResponse
    {
        $data = $request->validated();
        unset($data['website']);

        $data['ip_address'] = $request->ip();
        $data['status'] = 'new';

        $submission = ContactSubmission::create($data);

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $name = Str::uuid().'.'.$file->getClientOriginalExtension();

            $file->storeAs('contact/'.$submission->id, $name, 'local');

            $submission->update(['attachment' => $name]);
        }

        Mail::to($submission->email)
            ->send(new ContactReceived($submission));

        $notifyEmail = config('contact-inbox.notify_email');

        if ($notifyEmail) {
            Mail::to($notifyEmail)
                ->send(new AdminContactNotification($submission));

            // Same moment as the admin e-mail: push to all admin devices.
            dispatch(function () use ($submission) {
                AdminPushNotifier::notify(
                    'contact',
                    (string) $submission->id,
                    'Nieuw contactbericht: '.$submission->name,
                    mb_substr((string) $submission->message, 0, 120),
                    route('admin.contact-inbox.index', ['submission' => $submission->id], absolute: true)
                );
            })->afterResponse();
        }

        return response()->json([
            'message' => 'Bedankt! Je bericht is verzonden.',
        ], 201);
    }
}