<?php

namespace App\Http\Controllers;

use App\Mail\AdminAfspraakNotification;
use App\Mail\AfspraakReceived;
use App\Models\AfspraakSubmission;
use App\Http\Requests\StoreAfspraakSubmissionRequest;
use Illuminate\Http\JsonResponse;

class AfspraakController extends Controller
{
    /**
     * Store a new afspraak (home appointment) submission.
     */
    public function submit(StoreAfspraakSubmissionRequest $request): JsonResponse
    {
        $data = $request->validated();

        $year = now()->year;
        $last = AfspraakSubmission::where('afspraak_number', 'like', "AF-{$year}-%")
            ->orderByDesc('id')
            ->value('afspraak_number');

        if ($last) {
            $seq = (int) substr($last, -5);
            $seq = str_pad($seq + 1, 5, '0', STR_PAD_LEFT);
        } else {
            $seq = '00001';
        }

        $afspraakNumber = "AF-{$year}-{$seq}";

        $submission = AfspraakSubmission::create([
            'afspraak_number' => $afspraakNumber,
            'name'            => $data['name'],
            'email'           => $data['email'],
            'street'          => $data['street'],
            'phone'           => $data['phone'],
            'postcode'        => $data['postcode'],
            'house_number'    => $data['house_number'],
            'city'            => $data['city'],
            'device'          => $data['device'],
            'problem'         => $data['problem'],
            'preferred_date'  => $data['preferred_date'],
            'preferred_time'  => $data['preferred_time'],
            'status'          => 'new',
            'ip_address'      => $request->ip(),
        ]);

        $notifyEmail = config('contact-inbox.notify_email');

        dispatch(function () use ($submission, $notifyEmail) {
            \Mail::to($submission->email)
                ->send(new AfspraakReceived($submission));

            if ($notifyEmail) {
                \Mail::to($notifyEmail)
                    ->send(new AdminAfspraakNotification($submission));
            }
        })->afterResponse();

        return response()->json([
            'success'         => true,
            'afspraak_number' => $afspraakNumber,
            'message'         => 'Bedankt! We hebben uw aanvraag ontvangen en nemen spoedig contact met u op.',
        ], 201);
    }
}
