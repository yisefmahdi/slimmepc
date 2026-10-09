<?php

namespace App\Http\Controllers;

use App\Models\DigitalDownload;
use App\Models\DigitalFile;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DownloadController extends Controller
{
    /**
     * Serve a protected digital file.
     *
     * Allowed: admins, logged-in owners of a paid order containing a
     * digital product that references this file, or anyone holding a
     * valid per-order signed link (covers guest checkouts).
     * The order's payment_status is re-checked live on every hit, so a
     * cancelled/refunded order instantly loses access.
     */
    public function file(Request $request, DigitalFile $file): BinaryFileResponse
    {
        $order = null;
        $user = $request->user();

        if ($request->filled('order')) {
            if (! $request->hasValidSignature()) {
                abort(403, 'Deze downloadlink is ongeldig.');
            }
            $order = Order::where('order_number', (string) $request->query('order'))->first();
            if (! $order || ! $this->orderGrantsFile($order, $file)) {
                abort(403, 'Geen toegang tot dit bestand.');
            }
        } elseif ($user && $user->isAdmin()) {
            // Admins may always download (e.g. to verify a file).
        } elseif ($user) {
            $order = Order::where('user_id', $user->id)
                ->where('payment_status', 'paid')
                ->latest()
                ->get()
                ->first(fn (Order $o) => $this->orderGrantsFile($o, $file));
            if (! $order) {
                abort(403, 'Geen toegang tot dit bestand.');
            }
        } else {
            abort(403, 'Geen toegang tot dit bestand.');
        }

        if (! Storage::disk('local')->exists($file->path)) {
            abort(404, 'Bestand niet gevonden op de server.');
        }

        DigitalDownload::create([
            'digital_file_id' => $file->id,
            'order_id' => $order?->id,
            'user_id' => $user?->id,
            'ip' => $request->ip(),
        ]);
        $file->increment('downloads_count');

        // Sanitize the download filename: it originates from an admin upload
        // and ends up in the Content-Disposition header (CRLF/header injection).
        $filename = str_replace(["\r", "\n", '"'], '', basename((string) $file->name));
        if ($filename === '' || $filename === '.' || $filename === '..') {
            $filename = 'bestand-'.$file->id;
        }

        return response()->download(
            Storage::disk('local')->path($file->path),
            $filename,
            [
                // Never let browsers, proxies or search engines keep a copy
                'Cache-Control' => 'no-store, no-cache, must-revalidate',
                'Pragma' => 'no-cache',
                'X-Robots-Tag' => 'noindex, nofollow',
            ]
        );
    }

    protected function orderGrantsFile(Order $order, DigitalFile $file): bool
    {
        if ($order->payment_status !== 'paid') {
            return false;
        }

        $needle = '/download/bestand/' . $file->id;

        $order->loadMissing(['items.product']);

        return $order->items->contains(function ($item) use ($needle) {
            $product = $item->product;
            if (! $product || ! (bool) $product->is_digital) {
                return false;
            }

            return str_contains((string) $product->download_32bit_url, $needle)
                || str_contains((string) $product->download_64bit_url, $needle)
                || str_contains((string) $product->manual_url, $needle);
        });
    }
}
