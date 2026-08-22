<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VideoStreamController extends Controller
{
    /**
     * Stream a video from public/assets/video with HTTP Range support.
     *
     * The PHP built-in dev server (php artisan serve) does not honour Range
     * requests, so browsers refuse to play <video> files served from it.
     * This route serves the file through PHP and answers 206 Partial Content
     * so videos play both locally and in production.
     */
    public function show(Request $request, string $file)
    {
        $path = public_path('assets/video/' . basename($file));

        if (! is_file($path)) {
            abort(404);
        }

        $size = filesize($path);
        $mime = mime_content_type($path) ?: 'video/mp4';

        $handle = fopen($path, 'rb');
        if ($handle === false) {
            abort(404);
        }

        $range = $request->header('Range');

        if ($range && preg_match('/bytes=(\d*)-(\d*)/', $range, $m)) {
            $start = $m[1] === '' ? 0 : (int) $m[1];
            $end = $m[2] === '' ? $size - 1 : (int) $m[2];

            if ($start < 0 || $end >= $size || $start > $end) {
                fclose($handle);
                return response('', 416, ['Content-Range' => 'bytes */' . $size]);
            }

            fseek($handle, $start);
            $length = $end - $start + 1;
            $buffer = fread($handle, $length);
            fclose($handle);

            return response($buffer, 206, [
                'Content-Type' => $mime,
                'Content-Range' => 'bytes ' . $start . '-' . $end . '/' . $size,
                'Accept-Ranges' => 'bytes',
                'Content-Length' => $length,
            ]);
        }

        $buffer = fread($handle, $size);
        fclose($handle);

        return response($buffer, 200, [
            'Content-Type' => $mime,
            'Accept-Ranges' => 'bytes',
            'Content-Length' => $size,
        ]);
    }
}
