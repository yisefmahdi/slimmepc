<?php

namespace App\Support;

use App\Models\ContentBlock;
use App\Models\ContentMeta;
use Illuminate\Support\Facades\Cache;

class Cms
{
    public static function version(): string
    {
        return (string) Cache::rememberForever('cms.version', function () {
            return ContentMeta::where('meta_key', config('cms.cache_version_key'))
                ->value('meta_value') ?? '1';
        });
    }

    /**
     * All blocks of a page as [section][block_key] => value (json decoded).
     *
     * @return array<string, array<string, mixed>>
     */
    public static function page(string $page): array
    {
        $version = self::version();

        return Cache::remember("cms.page.{$page}.{$version}", now()->addMonth(), function () use ($page) {
            $blocks = ContentBlock::where('page', $page)
                ->orderBy('sort_order')
                ->get();

            $out = [];

            foreach ($blocks as $block) {
                $value = $block->json_value !== null
                    ? $block->json_value
                    : $block->value;

                $out[$block->section][$block->block_key] = $value;
            }

            return $out;
        });
    }

    public static function get(string $page, string $section, string $key, mixed $default = null): mixed
    {
        return self::page($page)[$section][$key] ?? $default;
    }

    /**
     * Design settings — returns a flat [key => value] array.
     * Handles both flat storage (seeder) and grouped storage (admin form).
     *
     * @return array<string, mixed>
     */
    public static function design(): array
    {
        $version = self::version();

        return Cache::remember("cms.design.{$version}", now()->addMonth(), function () {
            $raw = ContentMeta::where('meta_key', 'design')->value('meta_value');
            $data = $raw ? json_decode($raw, true) : [];

            if ($data && is_array(reset($data))) {
                $flat = [];

                foreach ($data as $group) {
                    if (is_array($group)) {
                        $flat = array_merge($flat, $group);
                    }
                }

                return $flat;
            }

            return $data;
        });
    }

    public static function designValue(string $key, mixed $default = null): mixed
    {
        $design = self::design();

        return $design[$key] ?? $default;
    }

    /**
     * Bump the cache version so the frontend picks up changes immediately.
     */
    public static function bust(): void
    {
        $meta = ContentMeta::updateOrCreate(
            ['meta_key' => config('cms.cache_version_key')],
            ['meta_value' => (string) now()->format('Uv')]
        );

        Cache::forget('cms.version');
        Cache::put('cms.version', $meta->meta_value, now()->addMonth());
    }
}

