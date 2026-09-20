<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMembershipRequest;
use App\Mail\MembershipWelcomeMail;
use App\Models\Membership;
use App\Models\MembershipSetting;
use App\Models\User;
use App\Services\Payments\MolliePaymentService;
use App\Support\Cms;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class LidmaatschapController extends Controller
{
    public function __construct(
        protected MolliePaymentService $payments,
    ) {}

    /**
     * Lid-worden formulier. Nooit cachen: het formulier bevat een CSRF-token.
     */
    public function show()
    {
        $c = Cms::page('home');
        $design = Cms::design();
        $price = MembershipSetting::price();

        return view('landing.lid-worden', compact('c', 'design', 'price'));
    }

    public function store(StoreMembershipRequest $request)
    {
        $data = $request->validated();

        // De prijs komt ALTIJD uit het dashboard, nooit uit het formulier.
        $price = MembershipSetting::price();
        if ($price <= 0) {
            $msg = 'De lidmaatschapsprijs is nog niet ingesteld. Probeer het later opnieuw.';
            if ($request->expectsJson()) {
                return response()->json(['message' => $msg, 'errors' => ['price' => [$msg]]], 422);
            }

            return back()->withInput()->withErrors(['price' => $msg]);
        }

        $email = strtolower(trim($data['customer_email']));

        $activeExists = Membership::where('customer_email', $email)
            ->where('payment_status', 'paid')
            ->where('end_date', '>', now())
            ->exists();

        if ($activeExists) {
            $msg = 'U heeft al een actief lidmaatschap. U kunt geen nieuw lidmaatschap aanvragen totdat het huidige is verlopen.';
            if ($request->expectsJson()) {
                return response()->json(['message' => $msg, 'errors' => ['customer_email' => [$msg]]], 422);
            }

            return back()->withInput()->withErrors(['customer_email' => $msg]);
        }

        $user = Auth::user();
        if ($user instanceof User) {
            if (! $user->klantnummer) {
                $user->update(['klantnummer' => $this->makeKlantnummer($data['name'])]);
                $user->refresh();
            }
            $klantnummer = $user->klantnummer;
            $userId = $user->id;
        } else {
            $klantnummer = $this->makeKlantnummer($data['name']);
            $userId = null;
        }

        $taxPct = 21.0;
        $subtotal = round($price / (1 + $taxPct / 100), 2);
        $tax = round($price - $subtotal, 2);

        $membership = Membership::create([
            'user_id' => $userId,
            'klantnummer' => $klantnummer,
            'customer_type' => $data['customer_type'] === 'zakelijk' ? 'business' : 'private',
            'customer_gender' => $data['customer_gender'],
            'name' => $data['name'],
            'customer_email' => $email,
            'customer_phone' => $data['customer_phone'],
            'customer_address' => $data['customer_address'],
            'postcode' => strtoupper(str_replace(' ', '', $data['postcode'])),
            'city' => $data['city'],
            'start_date' => now(),
            'end_date' => now()->addYear(),
            'total' => $price,
            'payment_status' => 'unpaid',
            'payment_method' => 'mollie',
            'terms_accepted' => true,
        ]);

        $membership->invoices()->create([
            'klantnummer' => $membership->klantnummer,
            'invoice_number' => 'LID-' . random_int(100000, 999999),
            'invoice_date' => now(),
            'payment_method' => 'mollie',
            'subtotal' => $subtotal,
            'tax_percentage' => $taxPct,
            'tax_amount' => $tax,
            'total' => $price,
        ]);

        if (! $this->payments->isConfigured()) {
            $msg = 'Aanmelding opgeslagen, maar de betaalkoppeling is nog niet ingesteld. Neem contact met ons op.';
            if ($request->expectsJson()) {
                return response()->json(['message' => $msg], 502);
            }

            return redirect()
                ->route('lidmaatschap.failed', ['lidmaatschap' => $membership->id])
                ->with('error', $msg);
        }

        try {
            $payment = $this->payments->createPayment(
                amount: $price,
                description: 'Slimme-PC lidmaatschap ' . $membership->klantnummer,
                redirectUrl: route('lidmaatschap.return', ['lidmaatschap' => $membership->id]),
                webhookUrl: $this->publicWebhookUrl($request),
                metadata: ['membership_id' => $membership->id],
            );
        } catch (\Throwable $e) {
            report($e);

            $msg = 'De betaling kon niet worden gestart. Probeer het opnieuw.';
            if ($request->expectsJson()) {
                return response()->json(['message' => $msg], 502);
            }

            return back()->withInput()->withErrors(['payment' => $msg]);
        }

        $membership->update(['mollie_payment_id' => $payment->id]);

        if ($request->expectsJson()) {
            return response()->json(['redirect' => $payment->getCheckoutUrl()], 201);
        }

        return redirect()->away($payment->getCheckoutUrl());
    }

    /**
     * Mollie server-to-server callback — source of truth.
     */
    public function webhook(Request $request)
    {
        $paymentId = (string) $request->input('id', '');
        if ($paymentId === '' || ! $this->payments->isConfigured()) {
            return response()->json(['status' => 'ignored'], 200);
        }

        try {
            $payment = $this->payments->getPayment($paymentId);
        } catch (\Throwable $e) {
            report($e);

            return response()->json(['status' => 'retry'], 500);
        }

        $membershipId = (int) ($payment->metadata->membership_id ?? 0);
        $membership = $membershipId ? Membership::find($membershipId) : null;
        if (! $membership) {
            return response()->json(['status' => 'unknown-membership'], 200);
        }

        if ($this->payments->isPaid($payment)) {
            $this->finalizeMembership($membership, $payment->method ?? null);
        } else {
            $status = (string) ($payment->status ?? '');
            if (in_array($status, ['canceled', 'expired', 'failed'], true)) {
                $membership->update(['payment_status' => 'cancelled']);
            }
        }

        return response()->json(['status' => 'ok']);
    }

    /**
     * Customer returns from Mollie — verify live and show result.
     */
    public function mollieReturn(Request $request, Membership $lidmaatschap)
    {
        if ($lidmaatschap->payment_status !== 'paid' && $lidmaatschap->mollie_payment_id && $this->payments->isConfigured()) {
            try {
                $payment = $this->payments->getPayment($lidmaatschap->mollie_payment_id);
                if ($this->payments->isPaid($payment)) {
                    $this->finalizeMembership($lidmaatschap, $payment->method ?? null);
                }
            } catch (\Throwable $e) {
                report($e);
            }
        }

        $lidmaatschap->refresh();

        if ($lidmaatschap->payment_status === 'paid') {
            return redirect()->route('lidmaatschap.success', ['lidmaatschap' => $lidmaatschap->id]);
        }

        return redirect()->route('lidmaatschap.failed', ['lidmaatschap' => $lidmaatschap->id]);
    }

    public function success(Request $request, Membership $lidmaatschap)
    {
        abort_unless($lidmaatschap->payment_status === 'paid', 404);

        $c = Cms::page('home');
        $design = Cms::design();

        return view('landing.lidmaatschap-success', compact('c', 'design', 'lidmaatschap'));
    }

    public function failed(Request $request, Membership $lidmaatschap)
    {
        $c = Cms::page('home');
        $design = Cms::design();

        return view('landing.lidmaatschap-failed', compact('c', 'design', 'lidmaatschap'));
    }

    /**
     * Markeer betaald + PDF-factuur + welkomstmail (idempotent).
     */
    protected function finalizeMembership(Membership $membership, ?string $mollieMethod = null): void
    {
        $membership->refresh();

        if ($membership->payment_status === 'paid') {
            return;
        }

        $membership->update([
            'payment_status' => 'paid',
            'payment_method' => $mollieMethod ?? $membership->payment_method,
        ]);

        $invoice = $membership->invoices()->latest('id')->first();
        if ($invoice && ! $invoice->pdf_path) {
            $pdf = Pdf::loadView('invoices.membership', [
                'invoice' => $invoice,
                'membership' => $membership,
            ]);
            $pdf->setPaper('a4', 'portrait');

            Storage::disk('local')->makeDirectory('invoices/membership');
            $path = 'invoices/membership/' . $invoice->invoice_number . '.pdf';
            Storage::disk('local')->put($path, $pdf->output());
            $invoice->update(['pdf_path' => $path]);
        }

        dispatch(function () use ($membership) {
            Mail::to($membership->customer_email)->send(new MembershipWelcomeMail($membership->fresh()));
        })->afterResponse();
    }

    protected function makeKlantnummer(string $name): string
    {
        $base = strtoupper(substr(preg_replace('/[^a-zA-Z]/u', '', $name) ?: 'LID', 0, 5));

        do {
            $number = 'SMP-' . $base . random_int(100000, 999999);
        } while (Membership::where('klantnummer', $number)->exists());

        return $number;
    }

    /**
     * Mollie must be able to reach the webhook URL — localhost never is.
     */
    protected function publicWebhookUrl(Request $request): ?string
    {
        $host = $request->getHost();
        if (in_array($host, ['127.0.0.1', 'localhost'], true)) {
            return null;
        }

        return route('lidmaatschap.webhook');
    }
}
