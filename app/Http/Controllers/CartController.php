<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CartService;
use App\Support\Cms;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class CartController extends Controller
{
    public function __construct(protected CartService $cartService) {}

    public function index(Request $request)
    {
        $c = Cms::page('home');
        $design = Cms::design();

        $cart = $this->cartService->resolveCart($request);
        $cart->load(['items.product.category', 'coupon']);
        $totals = $this->cartService->totals($cart);

        // Upsell - random featured or any 1
        $upsell = Product::where('status', true)->where('stock_status', 'in_stock')
            ->whereNotIn('id', $cart->items->pluck('product_id'))
            ->inRandomOrder()->first();

        // Queue cookie if newly created for guest
        $response = response()->view('landing.cart', compact('c', 'design', 'cart', 'totals', 'upsell'));
        if (!$request->user() && $cart->cart_token) {
            $response->withCookie(Cookie::forever(CartService::COOKIE_NAME, $cart->cart_token));
        }
        return $response;
    }

    public function count(Request $request)
    {
        $count = $this->cartService->countForRequest($request);
        $cart = $this->cartService->resolveCart($request);
        $totals = $this->cartService->totals($cart);
        return response()->json([
            'count' => $count,
            'subtotal' => $totals['subtotal'],
            'total' => $totals['total'],
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'nullable|integer|min:1|max:99',
        ], [
            'product_id.required' => 'Product is verplicht.',
            'product_id.exists' => 'Product niet gevonden.',
        ]);

        $product = Product::findOrFail($data['product_id']);

        if (!$product->status) {
            return response()->json(['message' => 'Dit product is niet beschikbaar.'], 422);
        }
        if ($product->stock_status !== 'in_stock') {
            return response()->json(['message' => 'Dit product is niet op voorraad.'], 422);
        }

        $qty = (int) ($data['quantity'] ?? 1);
        $cart = $this->cartService->resolveCart($request);
        $this->cartService->addItem($cart, $product, $qty);

        $cart->refresh()->load(['items.product.category', 'coupon']);
        $totals = $this->cartService->totals($cart);

        $addedItem = $cart->items->firstWhere('product_id', $product->id);
        $addedItem->loadMissing('product.category');

        $response = response()->json([
            'message' => 'Toegevoegd aan winkelwagen.',
            'count' => $totals['count'],
            'totals' => $totals,
            'item' => $addedItem ? [
                'id' => $addedItem->id,
                'quantity' => $addedItem->quantity,
                'price_snapshot' => $addedItem->price_snapshot,
                'product' => [
                    'id' => $product->id,
                    'title' => $product->title,
                    'slug' => $product->slug,
                    'brand' => $product->brand,
                    'category' => $product->category ? ['slug' => $product->category->slug, 'name' => $product->category->name] : null,
                    'image' => $product->main_image ?: ($product->gallery_images[0] ?? null),
                    'features' => $product->features,
                ],
            ] : null,
        ]);

        if (!$request->user() && $cart->cart_token) {
            $response->withCookie(Cookie::forever(CartService::COOKIE_NAME, $cart->cart_token));
        }

        return $response;
    }

    public function update(Request $request, int $item)
    {
        $data = $request->validate([
            'quantity' => 'required|integer|min:1|max:99',
        ]);

        $cart = $this->cartService->resolveCart($request);
        $cartItem = $cart->items()->where('id', $item)->firstOrFail();
        $cartItem->update(['quantity' => $data['quantity']]);

        $cart->refresh()->load(['items', 'coupon']);
        $totals = $this->cartService->totals($cart);

        // Recalculate item subtotal
        $itemSubtotal = round((float) $cartItem->fresh()->price_snapshot * (int) $data['quantity'], 2);

        return response()->json([
            'message' => 'Aantal bijgewerkt.',
            'item_subtotal' => $itemSubtotal,
            'totals' => $totals,
            'count' => $totals['count'],
        ]);
    }

    public function destroy(Request $request, int $item)
    {
        $cart = $this->cartService->resolveCart($request);
        $cartItem = $cart->items()->where('id', $item)->firstOrFail();
        $cartItem->delete();

        $cart->refresh()->load(['items', 'coupon']);
        $totals = $this->cartService->totals($cart);

        return response()->json([
            'message' => 'Product verwijderd.',
            'totals' => $totals,
            'count' => $totals['count'],
        ]);
    }

    public function clear(Request $request)
    {
        $cart = $this->cartService->resolveCart($request);
        $cart->items()->delete();
        $cart->update(['coupon_id' => null]);

        return response()->json([
            'message' => 'Winkelwagen geleegd.',
            'count' => 0,
            'totals' => $this->cartService->totals($cart->fresh()->load(['items', 'coupon'])),
        ]);
    }

    public function applyCoupon(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|max:50',
        ], [
            'code.required' => 'Vul een kortingscode in.',
        ]);

        $cart = $this->cartService->resolveCart($request);

        if ($cart->items()->count() === 0) {
            return response()->json(['message' => 'Je winkelwagen is leeg.'], 422);
        }

        $result = $this->cartService->validateCoupon($data['code'], $cart);
        if (!$result['valid']) {
            return response()->json(['message' => $result['message']], 422);
        }

        $coupon = $result['coupon'];
        $cart->update(['coupon_id' => $coupon->id]);
        $cart->refresh()->load(['items', 'coupon']);
        $totals = $this->cartService->totals($cart);

        return response()->json([
            'message' => 'Kortingscode toegepast.',
            'coupon' => ['code' => $coupon->code, 'discount' => $totals['discount']],
            'totals' => $totals,
        ]);
    }

    public function removeCoupon(Request $request)
    {
        $cart = $this->cartService->resolveCart($request);
        $cart->update(['coupon_id' => null]);
        $cart->refresh()->load(['items', 'coupon']);
        $totals = $this->cartService->totals($cart);

        return response()->json([
            'message' => 'Kortingscode verwijderd.',
            'totals' => $totals,
        ]);
    }
}
