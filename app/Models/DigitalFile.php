<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;

class DigitalFile extends Model
{
    protected $fillable = [
        'name',
        'path',
        'size',
        'mime',
        'uploaded_by',
        'downloads_count',
    ];

    protected $casts = [
        'size' => 'integer',
        'downloads_count' => 'integer',
    ];

    public const ALLOWED_EXTENSIONS = [
        'zip', 'iso', 'exe', 'msi', 'pdf', 'dmg', 'pkg', '7z', 'rar',
    ];

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function downloads()
    {
        return $this->hasMany(DigitalDownload::class);
    }

    /** Public static link pasted into product download fields. */
    public function routeUrl(): string
    {
        return route('download.file', ['file' => $this->id]);
    }

    /**
     * Personal signed link for one order (emailed to guests too).
     * Expires after 30 days (previously never); the signature itself is a
     * keyed HMAC over the URL (Laravel signed routes). The signature stays
     * valid within its lifetime, but DownloadController re-checks
     * payment_status=paid live on every hit.
     */
    public function signedUrlForOrder(Order $order): string
    {
        return URL::temporarySignedRoute(
            'download.file',
            now()->addDays(30),
            ['file' => $this->id, 'order' => $order->order_number]
        );
    }

    public function humanSize(): string
    {
        $bytes = (int) $this->size;
        if ($bytes < 1024) {
            return $bytes.' B';
        }
        $units = ['KB', 'MB', 'GB', 'TB'];
        $i = (int) floor(log($bytes, 1024));
        $i = min($i, count($units));

        return round($bytes / (1024 ** $i), $i >= 2 ? 2 : 0).' '.$units[$i - 1];
    }
}
