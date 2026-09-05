<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

class CartService
{
    public const COOKIE_NAME = 'cart_token';
    public const SHIPPING_COST = 6.95;
    public const FREE_SHIPPING_THRESHOLD = 75;

    public function resolveCart(Request $request): Cart
    {
        // Authenticated user -> cart by user_id
        if ($request->user()) {
            $cart = Cart::firstOrCreate(['user_id' => $request->user()->id]);
            // Merge guest cart if token cookie exists
            $token = $request->cookie(self::COOKIE_NAME);
            if ($token) {
                $guestCart = Cart::where('cart_token', $token)->whereNull('user_id')->first();
                if ($guestCart && $guestCart->id !== $cart->id) {
                    $this->mergeCarts($guestCart, $cart);
                    $guestCart->delete();
                }
            }
            return $cart->load(['items.product', 'coupon']);
        }

        // Guest -> cart by token
        $token = $request->cookie(self::COOKIE_NAME);
        if (!$token) {
            $token = (string) Str::uuid();
            // Cookie will be queued by caller
            Cookie::queue(Cookie::forever(self::COOKIE_NAME, $token));
            // Also set on request for immediate use
            $request->cookies->set(self::COOKIE_NAME, $token);
        }

        $cart = Cart::firstOrCreate(['cart_token' => $token], ['cart_token' => $token]);
        return $cart->load(['items.product', 'coupon']);
    }

    public function getOrCreateCartForRequest(Request $request): Cart
    {
        return $this->resolveCart($request);
    }

    public function mergeCarts(Cart $from, Cart $to): void
    {
        foreach ($from->items as $item) {
            $existing = $to->items()->where('product_id', $item->product_id)->first();
            if ($existing) {
                $existing->increment('quantity', $item->quantity);
            } else {
                $to->items()->create([
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price_snapshot' => $item->price_snapshot,
                ]);
            }
        }
        // If target has no coupon but source has one, copy it
        if (!$to->coupon_id && $from->coupon_id) {
            $to->update(['coupon_id' => $from->coupon_id]);
        }
    }

    public function addItem(Cart $cart, Product $product, int $quantity = 1): CartItem
    {
        $price = (float) $product->discounted_price;

        $item = $cart->items()->where('product_id', $product->id)->first();

        if ($item) {
            $item->increment('quantity', $quantity);
            // Update price_snapshot to current discounted price
            $item->update(['price_snapshot' => $price]);
            return $item->fresh();
        }

        return $cart->items()->create([
            'product_id' => $product->id,
            'quantity' => $quantity,
            'price_snapshot' => $price,
        ]);
    }

    public function totals(Cart $cart): array
    {
        $cart->loadMissing(['items', 'coupon']);
        $subtotal = 0;
        foreach ($cart->items as $item) {
            $subtotal += (float) $item->price_snapshot * (int) $item->quantity;
        }
        $subtotal = round($subtotal, 2);

        $discount = 0;
        $coupon = $cart->coupon;
        if ($coupon && $coupon->isActive()) {
            // Check min_amount
            if ($coupon->min_amount === null || $subtotal >= (float) $coupon->min_amount) {
                $discount = $coupon->discountAmount($subtotal);
            } else {
                // Coupon not applicable due to min_amount, detach?
                $discount = 0;
            }
        } elseif ($coupon && !$coupon->isActive()) {
            // Auto-detach expired coupon
            $cart->update(['coupon_id' => null]);
            $coupon = null;
        }

        $afterDiscount = round(max($subtotal - $discount, 0), 2);

        $shipping = 0;
        if ($afterDiscount > 0 && $afterDiscount < self::FREE_SHIPPING_THRESHOLD) {
            $shipping = self::SHIPPING_COST;
        }

        $total = round($afterDiscount + $shipping, 2);

        $count = $cart->items->sum('quantity');

        return [
            'subtotal' => $subtotal,
            'discount' => round($discount, 2),
            'after_discount' => $afterDiscount,
            'shipping' => $shipping,
            'total' => $total,
            'count' => (int) $count,
            'coupon' => $coupon,
        ];
    }

    public function countForRequest(Request $request): int
    {
        try {
            if ($request->user()) {
                $cart = Cart::where('user_id', $request->user()->id)->first();
                if (!$cart) return 0;
                return (int) $cart->items()->sum('quantity');
            }
            $token = $request->cookie(self::COOKIE_NAME);
            if (!$token) return 0;
            $cart = Cart::where('cart_token', $token)->whereNull('user_id')->first();
            if (!$cart) return 0;
            return (int) $cart->items()->sum('quantity');
        } catch (\Throwable $e) {
            return 0;
        }
    }

    public function validateCoupon(string $code, Cart $cart): array
    {
        $coupon = Coupon::where('code', Str::upper(trim($code)))->first();
        if (!$coupon) {
            return ['valid' => false, 'message' => 'Ongeldige kortingscode.'];
        }
        if (!$coupon->status) {
            return ['valid' => false, 'message' => 'Deze kortingscode is niet actief.'];
        }
        if ($coupon->start_date && now()->lt($coupon->start_date)) {
            return ['valid' => false, 'message' => 'Deze kortingscode is nog niet geldig.'];
        }
        if ($coupon->end_date && now()->gt($coupon->end_date)) {
            return ['valid' => false, 'message' => 'Deze kortingscode is verlopen.'];
        }
        if ($coupon->isExpired()) {
            return ['valid' => false, 'message' => 'Deze kortingscode is verlopen.'];
        }
        if ($coupon->isMaxedOut()) {
            return ['valid' => false, 'message' => 'Deze kortingscode heeft het maximale aantal gebruiken bereikt.'];
        }

        $totals = $this->totals($cart);
        if ($coupon->min_amount !== null && $totals['subtotal'] < (float) $coupon->min_amount) {
            return ['valid' => false, 'message' => 'Minimaal bestelbedrag voor deze code is €' . number_format($coupon->min_amount, 2, ',', '.') . '.'];
        }

        // Check single use per user/guest
        if ($coupon->is_single_use) {
            $userId = $cart->user_id;
            $token = $cart->cart_token;
            if ($userId) {
                $exists = \App\Models\CouponUsage::where('coupon_id', $coupon->id)->where('user_id', $userId)->exists();
                if ($exists) return ['valid' => false, 'message' => 'Je hebt deze code al gebruikt.'];
            } elseif ($token) {
                $exists = \App\Models\CouponUsage::where('coupon_id', $coupon->id)->where('guest_token', $token)->exists();
                if ($exists) return ['valid' => false, 'message' => 'Je hebt deze code al gebruikt.'];
            }
        }

        return ['valid' => true, 'coupon' => $coupon];
    }
}
