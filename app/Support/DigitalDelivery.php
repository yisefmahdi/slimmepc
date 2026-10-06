<?php

namespace App\Support;

use App\Models\DigitalFile;
use App\Models\Order;
use App\Models\Product;

/**
 * Resolves product download fields to protected file links.
 *
 * Product fields may hold either an internal file route
 * (/download/bestand/{id}) or a plain external https URL.
 * Internal links are served by DownloadController after an
 * ownership/signature check; external links pass through.
 */
class DigitalDelivery
{
    public static function fileIdFromUrl(?string $url): ?int
    {
        if (! $url) {
            return null;
        }
        if (preg_match('#/download/bestand/(\d+)#', $url, $m)) {
            return (int) $m[1];
        }

        return null;
    }

    public static function fileFromUrl(?string $url): ?DigitalFile
    {
        $id = self::fileIdFromUrl($url);

        return $id ? DigitalFile::find($id) : null;
    }

    /**
     * Deliverable URL for one raw product field value.
     * With an order context (guests included) an internal file becomes
     * a personal signed link; without it, the static route (works for
     * logged-in owners) or the external URL as-is.
     */
    public static function resolveUrl(?string $raw, ?Order $order = null): ?string
    {
        if (! $raw) {
            return null;
        }
        $file = self::fileFromUrl($raw);
        if (! $file) {
            return $raw;
        }

        return $order ? $file->signedUrlForOrder($order) : $file->routeUrl();
    }

    /**
     * [{label, url}] download buttons for a product, order-aware.
     */
    public static function linksForProduct(Product $product, ?Order $order = null): array
    {
        $fields = [
            'download_32bit_url' => 'Download 32-bit versie',
            'download_64bit_url' => 'Download 64-bit versie',
            'manual_url' => 'Installatiehandleiding',
        ];

        $links = [];
        foreach ($fields as $field => $label) {
            $url = self::resolveUrl($product->{$field} ?? null, $order);
            if ($url) {
                $links[] = ['label' => $label, 'url' => $url];
            }
        }

        return $links;
    }
}
