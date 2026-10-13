<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\BroadcastMail;
use App\Models\MassEmail;
use App\Models\User;
use App\Support\MailAudit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MassEmailController extends Controller
{
    public function showForm()
    {
        $emails = MassEmail::latest()->paginate(10);

        return view('admin.mass-email.index', compact('emails'));
    }

    public function sendMassEmail(Request $request)
    {
        $request->validate([
            'message_type' => 'required|in:special_offer,marketing,maintenance,important_notice',
            'message_content' => 'required|string',
        ]);

        $massEmail = MassEmail::create([
            'message_type' => $request->message_type,
            'message_content' => $request->message_content,
        ]);

        // Terugvalroute zonder JS: direct (geen queue — er draait geen worker).
        // Het formulier verstuurt normaal in delen via sendChunk().
        @set_time_limit(0);

        $sent = 0;
        $failed = [];

        User::whereNotNull('email')->orderBy('id')->chunk(50, function ($users) use ($massEmail, &$sent, &$failed) {
            foreach ($users as $user) {
                if (self::sendTo($user, $massEmail)) {
                    $sent++;
                } else {
                    $failed[] = $user->email;
                }
            }
        });

        $msg = $sent.' e-mail(s) geaccepteerd door mailserver.'
            . ($failed ? ' Mislukt: '.implode(', ', $failed) : ' Controleer na enkele minuten inbox én spam.');

        return back()->with($failed ? 'error' : 'success', $msg);
    }

    /**
     * Directe (synchrone) verzending in delen — init-aanroep maakt het
     * record aan en geeft het totaal terug, chunk-aanroepen versturen
     * steeds een schijfje gebruikers. Geen queue-worker nodig.
     */
    public function sendChunk(Request $request): JsonResponse
    {
        if ($request->filled('email_id')) {
            $data = $request->validate([
                'email_id' => 'required|integer|exists:mass_emails,id',
                'offset' => 'required|integer|min:0',
                'limit' => 'required|integer|min:1|max:50',
            ]);

            $email = MassEmail::findOrFail($data['email_id']);
            $users = User::whereNotNull('email')
                ->orderBy('id')
                ->skip($data['offset'])
                ->take($data['limit'])
                ->get(['id', 'name', 'email']);

            @set_time_limit(120);

            $sent = 0;
            $failed = [];

            foreach ($users as $user) {
                if (self::sendTo($user, $email)) {
                    $sent++;
                } else {
                    $failed[] = $user->email;
                }
            }

            $total = User::whereNotNull('email')->count();

            return response()->json([
                'sent' => $sent,
                'failed' => $failed,
                'total' => $total,
                'done' => ($data['offset'] + $users->count()) >= $total,
            ]);
        }

        $data = $request->validate([
            'message_type' => 'required|in:special_offer,marketing,maintenance,important_notice',
            'message_content' => 'required|string',
        ]);

        $email = MassEmail::create([
            'message_type' => $data['message_type'],
            'message_content' => $data['message_content'],
        ]);

        return response()->json([
            'email_id' => $email->id,
            'total' => User::whereNotNull('email')->count(),
        ]);
    }

    public function deleteMassEmail($id)
    {
        MassEmail::findOrFail($id)->delete();

        return back()->with('success', 'Bericht verwijderd.');
    }

    /**
     * Eén gebruiker versturen met één directe herpoging bij een vluchtige
     * fout; elke poging gaat naar de mail-send auditlog.
     */
    private static function sendTo(User $user, MassEmail $email): bool
    {
        $subject = 'mass-email:'.$email->message_type;
        $error = null;
        for ($attempt = 1; $attempt <= 2; $attempt++) {
            try {
                $start = microtime(true);
                Mail::to($user->email)->send(new BroadcastMail(
                    $user->name ?? 'Klant',
                    $email->message_type,
                    $email->message_content
                ));
                MailAudit::sent('mass-email', (string) $user->email, $subject, microtime(true) - $start);
                $error = null;
                break;
            } catch (\Throwable $e) {
                $error = $e->getMessage();
                if ($attempt === 1) {
                    sleep(1);
                }
            }
        }
        if ($error !== null) {
            MailAudit::failed('mass-email', (string) $user->email, $subject, (string) $error);
        }

        return $error === null;
    }
}
