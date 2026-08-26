<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRepairSubmissionRequest;
use App\Mail\AdminRepairNotification;
use App\Mail\RepairReceived;
use App\Models\RepairSubmission;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class RepairController extends Controller
{
    /**
     * Store a new repair request from the public reparatie-aanmelden form.
     */
    public function submit(StoreRepairSubmissionRequest $request): JsonResponse
    {
        $data = $request->validated();
        unset($data['website']);

        $data['ip_address'] = $request->ip();
        $data['status'] = 'new';
        $data['privacy'] = $request->boolean('privacy');
        $data['repair_number'] = $this->generateRepairNumber();

        $submission = RepairSubmission::create($data);

        if ($request->hasFile('photos')) {
            $photos = [];

            foreach ($request->file('photos') as $file) {
                if ($file->isValid()) {
                    $name = Str::uuid().'.'.$file->getClientOriginalExtension();
                    $file->storeAs('repair/'.$submission->id, $name, 'local');
                    $photos[] = $name;
                }
            }

            if ($photos) {
                $submission->update(['photos' => $photos]);
            }
        }

        $notifyEmail = config('contact-inbox.notify_email');

        dispatch(function () use ($submission, $notifyEmail) {
            Mail::to($submission->email)
                ->send(new RepairReceived($submission));

            if ($notifyEmail) {
                Mail::to($notifyEmail)
                    ->send(new AdminRepairNotification($submission));
            }
        })->afterResponse();

        return response()->json([
            'message' => 'Bedankt! Je reparatieaanvraag is verzonden.',
            'repair_number' => $submission->repair_number,
        ], 201);
    }

    /**
     * Generate a unique repair number (SP-YYYY-#####).
     */
    private function generateRepairNumber(): string
    {
        do {
            $number = 'SP-'.date('Y').'-'.str_pad(random_int(0, 99999), 5, '0');
        } while (RepairSubmission::where('repair_number', $number)->exists());

        return $number;
    }
}
