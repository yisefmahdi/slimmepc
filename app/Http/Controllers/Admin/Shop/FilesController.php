<?php

namespace App\Http\Controllers\Admin\Shop;

use App\Http\Controllers\Controller;
use App\Models\DigitalFile;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FilesController extends Controller
{
    /** Max single chunk size: 8 MB (keeps every request far below PHP limits). */
    public const CHUNK_MAX_KB = 8192;

    /** Allowed installer/document extensions. */
    public const ALLOWED_EXTENSIONS = [
        'zip', 'iso', 'exe', 'msi', 'pdf', 'dmg', 'pkg', '7z', 'rar',
    ];

    public function index(Request $request)
    {
        return view('admin.shop.files.index', [
            'extensions' => self::ALLOWED_EXTENSIONS,
        ]);
    }

    public function data(Request $request)
    {
        $query = DigitalFile::query()->with('uploader')->orderByDesc('id');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $perPage = (int) $request->integer('per_page', 15);
        $perPage = in_array($perPage, [10, 15, 25, 50]) ? $perPage : 15;

        $files = $query->paginate($perPage)->withQueryString();
        $files->getCollection()->transform(function (DigitalFile $f) {
            $f->route_url = $f->routeUrl();

            return $f;
        });

        return response()->json([
            'files' => $files,
            'counts' => [
                'total' => DigitalFile::count(),
                'bytes' => (int) DigitalFile::sum('size'),
            ],
        ]);
    }

    /**
     * Receive one upload chunk (small request, immune to PHP upload limits).
     */
    public function chunk(Request $request)
    {
        $data = $request->validate([
            'upload_id' => ['required', 'string', 'max:64', 'regex:/^[A-Za-z0-9_-]+$/'],
            'index' => ['required', 'integer', 'min:0', 'max:100000'],
            'total' => ['required', 'integer', 'min:1', 'max:100000'],
            'chunk' => ['required', 'file', 'max:' . self::CHUNK_MAX_KB],
        ]);

        $dir = 'tmp/chunks/' . $data['upload_id'];
        Storage::disk('local')->putFileAs($dir, $data['chunk'], (string) $data['index']);

        return response()->json(['message' => 'Chunk ontvangen.', 'index' => $data['index']]);
    }

    /**
     * Assemble all chunks into the final protected file.
     */
    public function complete(Request $request)
    {
        $data = $request->validate([
            'upload_id' => ['required', 'string', 'max:64', 'regex:/^[A-Za-z0-9_-]+$/'],
            'total' => ['required', 'integer', 'min:1', 'max:100000'],
            'name' => ['required', 'string', 'max:255'],
            'size' => ['required', 'integer', 'min:1'],
        ]);

        $this->pruneStaleChunks();

        $ext = strtolower(pathinfo($data['name'], PATHINFO_EXTENSION));
        if (! in_array($ext, self::ALLOWED_EXTENSIONS, true)) {
            $this->dropChunks($data['upload_id']);

            return response()->json([
                'message' => 'Bestandstype niet toegestaan. Toegestaan: ' . implode(', ', self::ALLOWED_EXTENSIONS),
            ], 422);
        }

        $disk = Storage::disk('local');
        $dir = 'tmp/chunks/' . $data['upload_id'];

        for ($i = 0; $i < $data['total']; $i++) {
            if (! $disk->exists($dir . '/' . $i)) {
                return response()->json([
                    'message' => 'Upload onvolledig — deel ' . ($i + 1) . ' ontbreekt. Probeer het opnieuw.',
                ], 422);
            }
        }

        $free = @disk_free_space($disk->path('')) ?: 0;
        if ($free < $data['size'] + (10 * 1024 * 1024)) {
            $this->dropChunks($data['upload_id']);

            return response()->json([
                'message' => 'Niet genoeg schijfruimte op de server voor dit bestand.',
            ], 422);
        }

        set_time_limit(0);

        $stored = 'digital/' . Str::random(40) . '.' . $ext;
        $target = $disk->path($stored);
        @mkdir(dirname($target), 0775, true);

        $out = fopen($target, 'wb');
        if (! $out) {
            $this->dropChunks($data['upload_id']);

            return response()->json(['message' => 'Kon het bestand niet opslaan op de server.'], 500);
        }

        try {
            for ($i = 0; $i < $data['total']; $i++) {
                $in = fopen($disk->path($dir . '/' . $i), 'rb');
                stream_copy_to_stream($in, $out);
                fclose($in);
            }
        } finally {
            fclose($out);
        }

        $this->dropChunks($data['upload_id']);

        clearstatcache(true, $target);
        $realSize = (int) filesize($target);
        if ($realSize !== (int) $data['size']) {
            @unlink($target);

            return response()->json(['message' => 'Upload beschadigd (grootte komt niet overeen). Probeer het opnieuw.'], 422);
        }

        $mime = (string) (mime_content_type($target) ?: null);

        $file = DigitalFile::create([
            'name' => basename($data['name']),
            'path' => $stored,
            'size' => $realSize,
            'mime' => $mime,
            'uploaded_by' => $request->user()?->id,
        ]);

        return response()->json([
            'message' => 'Bestand geüpload.',
            'file' => $file,
            'url' => $file->routeUrl(),
        ], 201);
    }

    public function destroy(DigitalFile $file)
    {
        if ($this->usedInProducts($file)) {
            return response()->json([
                'message' => 'Dit bestand is gekoppeld aan een product. Verwijder eerst de link uit het product.',
            ], 422);
        }

        Storage::disk('local')->delete($file->path);
        $file->delete();

        return response()->json(['message' => 'Bestand verwijderd.']);
    }

    /** Products whose download fields point at this file's route. */
    protected function usedInProducts(DigitalFile $file): bool
    {
        $needle = '/download/bestand/' . $file->id;

        return Product::where('download_32bit_url', 'like', '%' . $needle . '%')
            ->orWhere('download_64bit_url', 'like', '%' . $needle . '%')
            ->orWhere('manual_url', 'like', '%' . $needle . '%')
            ->exists();
    }

    protected function dropChunks(string $uploadId): void
    {
        Storage::disk('local')->deleteDirectory('tmp/chunks/' . $uploadId);
    }

    /** Remove abandoned chunk dirs older than 24 hours. */
    protected function pruneStaleChunks(): void
    {
        try {
            $disk = Storage::disk('local');
            if (! $disk->exists('tmp/chunks')) {
                return;
            }
            foreach ($disk->directories('tmp/chunks') as $dir) {
                $stamp = $disk->lastModified($dir . '/0');
                if ($stamp && $stamp < now()->subDay()->timestamp) {
                    $disk->deleteDirectory($dir);
                }
            }
        } catch (\Throwable $e) {
            // Best effort only — never block an upload on pruning.
        }
    }
}
