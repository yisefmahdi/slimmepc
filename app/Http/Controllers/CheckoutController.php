<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCheckoutRequest;
use App\Models\Address;
use App\Models\Order;
use App\Models\ShippingRate;
use App\Services\CartService;
use App\Services\Payments\MolliePaymentService;
use App\Support\Cms;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function __construct(
        protected CartService $cartService,
        protected MolliePaymentService $payments,
    ) {}

    public function index(Request $request)
    {
        $cart = $this->cartService->resolveCart($request);
        $cart->load(['items.product.category', 'coupon']);

        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Je winkelwagen is leeg.');
        }

        $method = $request->query('shipping_method', 'delivery');
        if (! in_array($method, ['delivery', 'pickup'], true)) {
            $method = 'delivery';
        }

        $totals = $this->cartService->totals($cart, $method);
        $rates = ShippingRate::where('is_active', true)->orderBy('sort_order')->get();

        $savedAddresses = collect();
        if ($request->user()) {
            $savedAddresses = Address::where('user_id', $request->user()->id)
                ->orderByDesc('id')->limit(10)->get();
        }

        $c = Cms::page('home');
        $design = Cms::design();

        return view('landing.checkout', compact('c', 'design', 'cart', 'totals', 'rates', 'method', 'savedAddresses'));
    }

    public function totals(Request $request)
    {
        $cart = $this->cartService->resolveCart($request);
        $cart->load(['items', 'coupon']);
        $method = $request->input('shipping_method', 'delivery');
        if (! in_array($method, ['delivery', 'pickup'], true)) {
            $method = 'delivery';
        }

        return response()->json($this->cartService->totals($cart, $method));
    }

    public function store(StoreCheckoutRequest $request)
    {
        $cart = $this->cartService->resolveCart($request);
        $cart->load(['items.product', 'coupon']);

        if ($cart->items->isEmpty()) {
            return response()->json(['message' => 'Je winkelwagen is leeg.'], 422);
        }

        // Refuse unavailable products
        foreach ($cart->items as $item) {
            if (! $item->product || ! $item->product->status || $item->product->stock_status !== 'in_stock') {
                return response()->json(['message' => 'Een product in je winkelwagen is niet meer beschikbaar.'], 422);
            }
        }

        $data = $request->validated();
        $method = $data['shipping_method'];
        $totals = $this->cartService->totals($cart, $method);

        $user = $request->user();

        // Reuse a saved address owned by the user, or create a new one
        $address = null;
        if (! empty($data['saved_address_id']) && $user) {
            $address = Address::where('id', $data['saved_address_id'])
                ->where('user_id', $user->id)->first();
        }

        if (! $address) {
            $isPickup = $method === 'pickup';
            $address = Address::create([
                'user_id' => $user?->id,
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'street' => $isPickup ? '-' : $data['street'],
                'house_number' => $isPickup ? '-' : $data['house_number'],
                'addition' => $data['addition'] ?? null,
                'postcode' => $isPickup ? '-' : strtoupper(str_replace(' ', '', $data['postcode'] ?? '')),
                'city' => $isPickup ? 'Apeldoorn (afhalen)' : $data['city'],
                'country' => $data['country'] ?? 'Nederland',
                'phone' => $data['phone'],
                'email' => $data['email'],
                'type' => 'billing',
            ]);
        }

        $klantnummer = $user?->klantnummer;
        if (! $klantnummer) {
            $klantnummer = 'SLP-'.str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        }

        $order = Order::create([
            'user_id' => $user?->id,
            'billing_address_id' => $address->id,
            'shipping_address_id' => $address->id,
            'cart_id' => $cart->id,
            'klantnummer' => $klantnummer,
            'customer_email' => $data['email'],
            'customer_phone' => $data['phone'],
            'subtotal' => $totals['subtotal_excl'],
            'tax_percentage' => $totals['tax_percentage'],
            'tax_amount' => $totals['tax'],
            'discount_code' => $totals['coupon']?->code,
            'coupon_id' => $totals['coupon']?->id,
            'discount_amount' => $totals['discount'],
            'shipping_method' => $method,
            'shipping_cost' => $totals['shipping'],
            'total_price' => $totals['total'],
            'payment_status' => 'pending',
            'payment_method' => 'mollie',
            'order_status' => 'pending',
        ]);

        foreach ($cart->items as $item) {
            $order->items()->create([
                'product_id' => $item->product_id,
                'product_name' => $item->product->title,
                'product_price' => $item->price_snapshot,
                'quantity' => $item->quantity,
                'total_price' => round((float) $item->price_snapshot * (int) $item->quantity, 2),
            ]);
        }

        if (! $this->payments->isConfigured()) {
            return response()->json([
                'message' => 'Bestelling aangemaakt, maar de betaalkoppeling (MOLLIE_KEY) is nog niet ingesteld.',
                'order_number' => $order->order_number,
                'redirect' => route('payment.failed', ['order' => $order->id]),
            ], 201);
        }

        $webhookUrl = $this->publicWebhookUrl($request);

        try {
            $payment = $this->payments->createPayment(
                amount: (float) $order->total_price,
                description: 'Slimme-PC bestelling '.$order->order_number,
                redirectUrl: route('payment.return', ['order' => $order->id]),
                webhookUrl: $webhookUrl,
                metadata: ['order_id' => $order->id, 'cart_id' => $cart->id],
            );
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'message' => 'De betaling kon niet worden gestart. Probeer het opnieuw of kies een andere betaalmethode.',
                'order_number' => $order->order_number,
            ], 502);
        }

        $order->update(['mollie_payment_id' => $payment->id]);

        return response()->json([
            'message' => 'Bestelling aangemaakt.',
            'order_number' => $order->order_number,
            'redirect' => $payment->getCheckoutUrl(),
        ], 201);
    }

    /**
     * Mollie must be able to reach the webhook URL — localhost never is.
     * Returns null locally (payment is then confirmed via the return URL),
     * and the public URL on a real server where the webhook is the source of truth.
     */
    protected function publicWebhookUrl(Request $request): ?string
    {
        $host = $request->getHost();

        if (in_array($host, ['localhost', '127.0.0.1', '::1'], true) || str_ends_with($host, '.test') || str_ends_with($host, '.local')) {
            return null;
        }

        return $request->getSchemeAndHttpHost().'/payment/webhook';
    }
}
