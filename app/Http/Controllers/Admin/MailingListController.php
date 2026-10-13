<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\CustomEmail;
use App\Models\MailingList;
use App\Support\MailAudit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MailingListController extends Controller
{
    public function index()
    {
        $emails = MailingList::latest()->get();

        return view('admin.mailinglist.index', compact('emails'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:mailing_list,email',
            'name' => 'nullable|string|max:255',
        ]);

        MailingList::create($request->only('email', 'name'));

        return redirect()->back()->with('success', 'E-mail toegevoegd.');
    }

    public function destroy($id)
    {
        MailingList::findOrFail($id)->delete();

        return redirect()->back()->with('success', 'E-mail verwijderd.');
    }

    public function sendForm()
    {
        $emails = MailingList::orderBy('email')->get();

        return view('admin.mailinglist.send', compact('emails'));
    }

    public function send(Request $request)
    {
        $request->validate([
            'recipients' => 'required|array|min:1',
            'recipients.*' => 'email',
            // Newlines in the subject would split mail headers (header
            // injection) — admin input still goes to the wire verbatim.
            'subject' => ['required', 'string', 'max:255', 'not_regex:/[\r\n]/'],
            'message' => 'required|string',
            'type' => 'nullable|string|max:50',
        ]);

        // Let op: grote lijsten via dit formulier zonder JS lopen in één
        // request (time-out-risico) — het formulier verstuurt daarom altijd
        // in delen via sendChunk(). Dit blijft als terugvalroute.
        [$sent, $failed] = self::sendBatch(
            $request->recipients,
            $request->subject,
            $request->message,
            $request->type
        );

        $msg = $sent.' e-mail(s) direct verzonden.'
            . ($failed ? ' Mislukt: '.implode(', ', $failed) : '');

        return redirect()->back()->with($failed ? 'error' : 'success', $msg);
    }

    /**
     * Directe (synchrone) verzending in kleine delen — aangeroepen per chunk
     * vanuit het formulier (JS), zodat geen queue-worker nodig is en geen
     * PHP-timeout kan optreden bij grote lijsten. Max 50 per request.
     */
    public function sendChunk(Request $request): JsonResponse
    {
        $data = $request->validate([
            'recipients' => 'required|array|min:1|max:50',
            'recipients.*' => 'email',
            'subject' => ['required', 'string', 'max:255', 'not_regex:/[\r\n]/'],
            'message' => 'required|string',
            'type' => 'nullable|string|max:50',
        ]);

        @set_time_limit(120);

        [$sent, $failed] = self::sendBatch(
            $data['recipients'],
            $data['subject'],
            $data['message'],
            $data['type']
        );

        return response()->json(['sent' => $sent, 'failed' => $failed]);
    }

    /**
     * Gedeelde directe verzending (geen queue): per ontvanger try/catch,
     * zodat één fout adres de rest nooit blokkeert. Eén directe herpoging
     * bij een vluchtige fout; elke poging gaat naar de mail-send auditlog.
     *
     * @return array{0: int, 1: list<string>}
     */
    private static function sendBatch(array $recipients, string $subject, string $message, ?string $type): array
    {
        $sent = 0;
        $failed = [];

        foreach ($recipients as $recipient) {
            $start = microtime(true);
            $error = null;
            for ($attempt = 1; $attempt <= 2; $attempt++) {
                try {
                    Mail::to($recipient)->send(new CustomEmail($subject, $message, $type));
                    $error = null;
                    break;
                } catch (\Throwable $e) {
                    $error = $e->getMessage();
                    if ($attempt === 1) {
                        sleep(1);
                    }
                }
            }
            if ($error === null) {
                MailAudit::sent('mailinglist', $recipient, $subject, microtime(true) - $start);
                $sent++;
            } else {
                MailAudit::failed('mailinglist', $recipient, $subject, (string) $error);
                $failed[] = $recipient;
            }
        }

        return [$sent, $failed];
    }
}
