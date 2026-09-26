<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\TechnicianInvoiceMail;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Membership;
use App\Models\TechnicianForm;
use App\Models\TechnicianSetting;
use App\Models\User;
use App\Services\Payments\MolliePaymentService;
use App\Support\Cms;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class TechnicianController extends Controller
{
    public function technicialogin(): View
    {
        $c = Cms::page('home');
        $design = Cms::design();

        return view('landing.technician-login', compact('c', 'design'));
    }

    public function loginSubmit(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'klantnummer' => 'required|string|max:30',
        ]);

        if (! Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            return back()->withErrors(['email' => 'Verkeerde inloggegevens.'])->withInput();
        }

        $user = Auth::user();

        if (! $user instanceof User || $user->role !== 'technician') {
            Auth::logout();

            return back()->withErrors(['email' => 'Je hebt geen toegang tot deze pagina.']);
        }

        $clientExists = User::where('klantnummer', $request->input('klantnummer'))->exists();

        if (! $clientExists) {
            Auth::logout();

            return back()->withErrors(['klantnummer' => 'Klantnummer staat niet op ons data.'])->withInput();
        }

        return redirect()->route('technician.payment', ['klantnummer' => $request->input('klantnummer')]);
    }

    public function __construct(
        protected MolliePaymentService $payments,
    ) {}

    /**
     * Betaalformulier voor een klant. Toont lidmaatschapsvoordelen (check op e-mail).
     */
    public function paymentPage(string $klantnummer): View
    {
        $client = User::where('klantnummer', $klantnummer)->firstOrFail();

        $c = Cms::page('home');
        $design = Cms::design();
        $pricing = $this->pricingFor($client);

        return view('landing.technician-payment', compact('c', 'design', 'client', 'pricing'));
    }

    /**
     * Prijzen + lidmaatschapsvoordelen voor een klant (check op e-mail).
     *
     * @return array{hour_price:float,travel_cost:float,quarter_price:float,is_member:bool,member_discount_type:string,member_discount_value:float,member_free_travel:bool}
     */
    protected function pricingFor(User $client): array
    {
        $hourPrice = (float) (TechnicianSetting::getValue('hour_price') ?? 0);
        $travelCost = (float) (TechnicianSetting::getValue('travel_cost') ?? 0);

        $isMember = Membership::where('customer_email', strtolower(trim((string) $client->email)))
            ->where('payment_status', 'paid')
            ->where('end_date', '>', now())
            ->exists();

        $discountType = $isMember ? (string) (TechnicianSetting::getValue('member_discount_type') ?? 'none') : 'none';
        if (! in_array($discountType, ['percent', 'fixed'], true)) {
            $discountType = 'none';
        }

        return [
            'hour_price' => $hourPrice,
            'travel_cost' => $travelCost,
            'quarter_price' => round($hourPrice / 4, 2),
            'is_member' => $isMember,
            'member_discount_type' => $discountType,
            'member_discount_value' => $isMember ? (float) (TechnicianSetting::getValue('member_discount_value') ?? 0) : 0,
            'member_free_travel' => $isMember && (string) (TechnicianSetting::getValue('member_free_travel') ?? '0') === '1',
        ];
    }

    /**
     * Volledige berekening: kwartieren × kwartierprijs + reiskosten − lidkorting − coupon → 21% btw eruit.
     *
     * @return array{minutes:int,quarters:int,quarter_price:float,travel:float,bruto:float,member_discount:float,after_member:float,coupon:array{id:int,code:string,discount:float}|null,coupon_discount:float,net:float,btw:float,subtotal:float}|array{error:string}
     */
    protected function calculate(User $client, string $start, string $end, ?string $couponCode): array
    {
        $s = \Carbon\Carbon::createFromFormat('H:i', $start);
        $e = \Carbon\Carbon::createFromFormat('H:i', $end);
        $minutes = $s->diffInMinutes($e);

        if ($minutes < 5) {
            return ['error' => 'De eindtijd moet na de starttijd zijn en minimaal 5 minuten.'];
        }

        $pricing = $this->pricingFor($client);
        $quarters = (int) ceil($minutes / 15);
        $travel = $pricing['member_free_travel'] ? 0.0 : $pricing['travel_cost'];
        $bruto = round($quarters * $pricing['quarter_price'] + $travel, 2);

        $memberDiscount = 0.0;
        if ($pricing['member_discount_type'] === 'percent' && $pricing['member_discount_value'] > 0) {
            $memberDiscount = round($bruto * $pricing['member_discount_value'] / 100, 2);
        } elseif ($pricing['member_discount_type'] === 'fixed' && $pricing['member_discount_value'] > 0) {
            $memberDiscount = (float) min($pricing['member_discount_value'], $bruto);
        }
        $afterMember = round($bruto - $memberDiscount, 2);

        $coupon = null;
        $couponDiscount = 0.0;
        if ($couponCode !== null && $couponCode !== '') {
            $check = $this->checkCouponModel($client, strtoupper(trim($couponCode)), $afterMember);
            if (isset($check['error'])) {
                return ['error' => $check['error']];
            }
            $coupon = ['id' => $check['coupon']->id, 'code' => $check['coupon']->code, 'discount' => $check['discount']];
            $couponDiscount = $check['discount'];
        }

        $net = round($afterMember - $couponDiscount, 2);
        $btw = round($net * 21 / 121, 2);

        return [
            'minutes' => $minutes,
            'quarters' => $quarters,
            'quarter_price' => $pricing['quarter_price'],
            'travel' => $travel,
            'bruto' => $bruto,
            'member_discount' => $memberDiscount,
            'after_member' => $afterMember,
            'coupon' => $coupon,
            'coupon_discount' => $couponDiscount,
            'net' => $net,
            'btw' => $btw,
            'subtotal' => round($net - $btw, 2),
            'is_member' => $pricing['is_member'],
        ];
    }

    /**
     * @return array{coupon:Coupon,discount:float}|array{error:string}
     */
    protected function checkCouponModel(User $client, string $code, float $amount): array
    {
        $coupon = Coupon::where('code', $code)->first();

        if (! $coupon || ! $coupon->isActive()) {
            return ['error' => 'De kortingscode is ongeldig.'];
        }
        if ($coupon->isExpired()) {
            return ['error' => 'De kortingscode is verlopen.'];
        }
        if ($coupon->isMaxedOut()) {
            return ['error' => 'De kortingscode is al maximaal gebruikt.'];
        }
        $alreadyUsed = CouponUsage::where('coupon_id', $coupon->id)
            ->where('user_id', $client->id)
            ->exists();
        if ($alreadyUsed) {
            return ['error' => 'U heeft deze kortingscode al gebruikt.'];
        }

        return ['coupon' => $coupon, 'discount' => $coupon->discountAmount($amount)];
    }

    /** Live prijscheck voor het formulier (AJAX). */
    public function checkCoupon(Request $request): JsonResponse
    {
        $data = $request->validate([
            'klantnummer' => 'required|string|max:30',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'coupon_code' => 'required|string|max:30',
        ]);

        $client = User::where('klantnummer', $data['klantnummer'])->first();
        if (! $client) {
            return response()->json(['error' => 'Klant niet gevonden.'], 422);
        }

        $calc = $this->calculate($client, $data['start_time'], $data['end_time'], $data['coupon_code']);
        if (isset($calc['error'])) {
            return response()->json(['error' => $calc['error']], 422);
        }

        return response()->json([
            'success' => true,
            'discount' => $calc['coupon_discount'],
            'member_discount' => $calc['member_discount'],
            'travel' => $calc['travel'],
            'total' => $calc['net'],
            'subtotal' => $calc['subtotal'],
            'btw' => $calc['btw'],
        ]);
    }

    /** Live totaal zonder coupon (AJAX). */
    public function quote(Request $request): JsonResponse
    {
        $data = $request->validate([
            'klantnummer' => 'required|string|max:30',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        $client = User::where('klantnummer', $data['klantnummer'])->first();
        if (! $client) {
            return response()->json(['error' => 'Klant niet gevonden.'], 422);
        }

        $calc = $this->calculate($client, $data['start_time'], $data['end_time'], null);
        if (isset($calc['error'])) {
            return response()->json(['error' => $calc['error']], 422);
        }

        return response()->json([
            'success' => true,
            'minutes' => $calc['minutes'],
            'quarters' => $calc['quarters'],
            'quarter_price' => $calc['quarter_price'],
            'travel' => $calc['travel'],
            'bruto' => $calc['bruto'],
            'member_discount' => $calc['member_discount'],
            'total' => $calc['net'],
            'subtotal' => $calc['subtotal'],
            'btw' => $calc['btw'],
            'is_member' => $calc['is_member'],
        ]);
    }

    public function storePaymentForm(Request $request)
    {
        $data = $request->validate([
            'klantnummer' => 'required|string|max:30',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'description' => 'nullable|string|max:2000',
            'work_done' => 'nullable|string|max:2000',
            'advice' => 'nullable|string|max:2000',
            'rating' => 'nullable|integer|min:1|max:5',
            'comment' => 'nullable|string|max:2000',
            'coupon_code' => 'nullable|string|max:30',
        ]);

        $client = User::where('klantnummer', $data['klantnummer'])->first();
        if (! $client) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Klant niet gevonden.', 'errors' => ['klantnummer' => ['Klant niet gevonden.']]], 422);
            }

            return back()->withInput()->withErrors(['klantnummer' => 'Klant niet gevonden.']);
        }

        $calc = $this->calculate($client, $data['start_time'], $data['end_time'], $data['coupon_code'] ?? null);
        if (isset($calc['error'])) {
            if ($request->expectsJson()) {
                return response()->json(['message' => $calc['error'], 'errors' => ['end_time' => [$calc['error']]]], 422);
            }

            return back()->withInput()->withErrors(['end_time' => $calc['error']]);
        }

        $pricing = $this->pricingFor($client);

        $form = TechnicianForm::create([
            'user_id' => $client->id,
            'technician_id' => Auth::id(),
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'duration_minutes' => $calc['minutes'],
            'quarter_count' => $calc['quarters'],
            'quarter_price' => $calc['quarter_price'],
            'travel_cost' => $calc['travel'],
            'subtotal' => $calc['subtotal'],
            'btw' => $calc['btw'],
            'total' => $calc['net'],
            'description' => $data['description'] ?? null,
            'work_done' => $data['work_done'] ?? null,
            'advice' => $data['advice'] ?? null,
            'rating' => $data['rating'] ?? null,
            'comment' => $data['comment'] ?? null,
            'member_discount' => $calc['member_discount'],
            'coupon_id' => $calc['coupon']['id'] ?? null,
            'coupon_discount' => $calc['coupon_discount'],
            'payment_status' => 'unpaid',
        ]);

        $form->technicianInvoice()->create([
            'invoice_date' => now()->format('Y-m-d'),
            'subtotal' => $calc['subtotal'],
            'btw' => $calc['btw'],
            'total' => $calc['net'],
            'status' => 'unpaid',
        ]);

        if ($calc['coupon']) {
            CouponUsage::firstOrCreate(
                ['coupon_id' => $calc['coupon']['id'], 'user_id' => $client->id],
                ['used_at' => now()]
            );
            $coupon = Coupon::find($calc['coupon']['id']);
            $coupon?->increment('used_count');
        }

        if (! $this->payments->isConfigured()) {
            $msg = 'Formulier opgeslagen, maar de betaalkoppeling is nog niet ingesteld.';
            if ($request->expectsJson()) {
                return response()->json(['message' => $msg], 502);
            }

            return redirect()->route('technician.failed', ['form' => $form->id])->with('error', $msg);
        }

        try {
            $payment = $this->payments->createPayment(
                amount: (float) $calc['net'],
                description: 'Slimme-PC monteur #' . $form->id . ' (' . $client->klantnummer . ')',
                redirectUrl: route('technician.return', ['form' => $form->id]),
                webhookUrl: $this->publicWebhookUrl($request),
                metadata: ['technician_form_id' => $form->id],
            );
        } catch (\Throwable $e) {
            report($e);

            $msg = 'De betaling kon niet worden gestart. Probeer het opnieuw.';
            if ($request->expectsJson()) {
                return response()->json(['message' => $msg], 502);
            }

            return back()->withInput()->withErrors(['payment' => $msg]);
        }

        $form->update(['mollie_payment_id' => $payment->id]);

        if ($request->expectsJson()) {
            return response()->json(['redirect' => $payment->getCheckoutUrl()], 201);
        }

        return redirect()->away($payment->getCheckoutUrl());
    }

    /** Mollie server-to-server callback — source of truth. */
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

        $formId = (int) ($payment->metadata->technician_form_id ?? 0);
        $form = $formId ? TechnicianForm::find($formId) : null;
        if (! $form) {
            return response()->json(['status' => 'unknown-form'], 200);
        }

        if ($this->payments->isPaid($payment)) {
            $this->finalizeForm($form, $payment->method ?? null);
        } else {
            $status = (string) ($payment->status ?? '');
            if (in_array($status, ['canceled', 'expired', 'failed'], true)) {
                $form->update(['payment_status' => 'cancelled']);
            }
        }

        return response()->json(['status' => 'ok']);
    }

    /** Customer returns from Mollie — verify live and show result. */
    public function mollieReturn(Request $request, TechnicianForm $form)
    {
        if ($form->payment_status !== 'paid' && $form->mollie_payment_id && $this->payments->isConfigured()) {
            try {
                $payment = $this->payments->getPayment($form->mollie_payment_id);
                if ($this->payments->isPaid($payment)) {
                    $this->finalizeForm($form, $payment->method ?? null);
                }
            } catch (\Throwable $e) {
                report($e);
            }
        }

        $form->refresh();

        if ($form->payment_status === 'paid') {
            return redirect()->route('technician.success', ['form' => $form->id]);
        }

        return redirect()->route('technician.failed', ['form' => $form->id]);
    }

    public function success(Request $request, TechnicianForm $form)
    {
        abort_unless($form->payment_status === 'paid', 404);

        // Voor veiligheid: na een geslaagde betaling de monteur direct uitloggen.
        if (Auth::check()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        $c = Cms::page('home');
        $design = Cms::design();

        return view('landing.technician-success', compact('c', 'design', 'form'));
    }

    public function failed(Request $request, TechnicianForm $form)
    {
        $c = Cms::page('home');
        $design = Cms::design();

        return view('landing.technician-failed', compact('c', 'design', 'form'));
    }

    /**
     * Markeer betaald + PDF-factuur + mail naar de klant (idempotent).
     */
    protected function finalizeForm(TechnicianForm $form, ?string $mollieMethod = null): void
    {
        $form->refresh();

        if ($form->payment_status === 'paid') {
            return;
        }

        $form->update([
            'payment_status' => 'paid',
            'payment_method' => $mollieMethod ?? $form->payment_method ?? 'mollie',
        ]);

        $invoice = $form->technicianInvoice()->latest('id')->first();        if ($invoice) {
            $invoice->update(['status' => 'paid']);

            if (! $invoice->pdf_path) {
                $pdf = Pdf::loadView('invoices.technician', [
                    'invoice' => $invoice,
                    'form' => $form->load('user'),
                ]);
                $pdf->setPaper('a4', 'portrait');

                Storage::disk('local')->makeDirectory('invoices/technician');
                $path = 'invoices/technician/' . $invoice->invoice_number . '.pdf';
                Storage::disk('local')->put($path, $pdf->output());
                $invoice->update(['pdf_path' => $path]);
            }
        }

        dispatch(function () use ($form) {
            $fresh = $form->fresh()->load('user');
            Mail::to($fresh->user->email)->send(new TechnicianInvoiceMail($fresh));
        })->afterResponse();
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

        return route('technician.webhook');
    }
}
