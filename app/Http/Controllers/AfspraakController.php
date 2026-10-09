<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAfspraakSubmissionRequest;
use App\Mail\AdminAfspraakNotification;
use App\Mail\AfspraakReceived;
use App\Models\AfspraakSubmission;
use App\Services\AdminPushNotifier;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;

class AfspraakController extends Controller
{
    /**
     * Store a new afspraak (home appointment) submission.
     */
    public function submit(StoreAfspraakSubmissionRequest $request): JsonResponse
    {
        $data = $request->validated();
        unset($data['website']);

        // Sequential numbers race under concurrent submits: retry on a
        // duplicate-key hit (afspraak_number is unique) instead of 500ing.
        $submission = null;
        $attempts = 0;
        while ($submission === null && $attempts < 5) {
            $attempts++;
            $afspraakNumber = $this->nextNumber();
            try {
                $submission = AfspraakSubmission::create([
                    'afspraak_number' => $afspraakNumber,
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'street' => $data['street'],
                    'phone' => $data['phone'],
                    'postcode' => $data['postcode'],
                    'house_number' => $data['house_number'],
                    'city' => $data['city'],
                    'device' => $data['device'],
                    'problem' => $data['problem'],
                    'preferred_date' => $data['preferred_date'],
                    'preferred_time' => $data['preferred_time'],
                    'status' => 'new',
                    'ip_address' => $request->ip(),
                ]);
            } catch (QueryException $e) {
                // 23000 = duplicate entry: another request grabbed the same
                // sequence number concurrently — retry with the next one.
                if (($e->errorInfo[0] ?? null) !== '23000' || $attempts >= 5) {
                    throw $e;
                }
            }
        }

        $afspraakNumber = $submission->afspraak_number;

        $notifyEmail = config('contact-inbox.notify_email');

        dispatch(function () use ($submission, $notifyEmail) {
            \Mail::to($submission->email)
                ->send(new AfspraakReceived($submission));

            if ($notifyEmail) {
                \Mail::to($notifyEmail)
                    ->send(new AdminAfspraakNotification($submission));

                // Same moment as the admin e-mail: push to all admin devices.
                AdminPushNotifier::notify(
                    'afspraak',
                    (string) $submission->afspraak_number,
                    'Nieuwe afspraak: '.$submission->afspraak_number,
                    ($submission->name ?? '').' — '.($submission->preferred_date ?? ''),
                    route('admin.afspraak-aanvragen.index', absolute: true)
                );
            }
        })->afterResponse();

        return response()->json([
            'success' => true,
            'afspraak_number' => $afspraakNumber,
            'message' => 'Bedankt! We hebben uw aanvraag ontvangen en nemen spoedig contact met u op.',
        ], 201);
    }

    protected function nextNumber(): string
    {
        $year = now()->year;
        $last = AfspraakSubmission::where('afspraak_number', 'like', "AF-{$year}-%")
            ->orderByDesc('id')
            ->value('afspraak_number');

        if ($last) {
            $seq = str_pad((int) substr($last, -5) + 1, 5, '0', STR_PAD_LEFT);
        } else {
            $seq = '00001';
        }

        return "AF-{$year}-{$seq}";
    }
}
