<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContentBlock;
use App\Models\ContentMeta;
use App\Support\Cms;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ContentController extends Controller
{
    /**
     * Show the CMS design (SEO) editor as a separate page.
     */
    public function editDesign(): View
    {
        $design = Cms::design();
        $designGroups = config('cms.design');

        return view('admin.content.design', [
            'title' => 'Home-page - Ontwerp & SEO',
            'design' => $design,
            'designGroups' => $designGroups,
        ]);
    }

    /**
     * Show a single section of a page as a separate page.
     */
    public function editSection(string $page, string $section): View
    {
        $pages = config('cms.pages');

        if (! array_key_exists($page, $pages) || ! array_key_exists($section, $pages[$page]['sections'])) {
            abort(404);
        }

        $sectionConfig = $pages[$page]['sections'][$section];
        $content = Cms::page($page);

        return view('admin.content.section', [
            'title' => $pages[$page]['label'] . ' - ' . $sectionConfig['label'],
            'page' => $page,
            'pageLabel' => $pages[$page]['label'],
            'sectionKey' => $section,
            'section' => $sectionConfig,
            'content' => $content,
        ]);
    }

    /**
     * Save one section of one page (accordion "save" button per section).
     */
    public function updateSection(Request $request, string $page, string $section): \Illuminate\Http\JsonResponse
    {
        $schema = config("cms.pages.{$page}.sections.{$section}");

        if (! $schema) {
            return response()->json(['message' => 'Sectie niet gevonden.'], 404);
        }

        $payload = $request->validate(['blocks' => ['required', 'array']]);
        $saved = [];

        DB::transaction(function () use ($payload, $page, $section, $schema, $request, &$saved) {
            foreach ($payload['blocks'] as $key => $value) {
                $blockSchema = $schema['blocks'][$key] ?? null;

                if (! $blockSchema) {
                    continue;
                }

                $jsonValue = null;
                $stringValue = null;

                if ($blockSchema['type'] === 'json') {
                    $jsonValue = $this->normalizeJson($request, $key, $value, $blockSchema);
                } else {
                    $file = $request->file("blocks.{$key}_file");

                    if (! $file && $value instanceof UploadedFile) {
                        $file = $value;
                    }

                    if (($blockSchema['type'] === 'image' || $blockSchema['type'] === 'video') && $file) {
                        if ($blockSchema['type'] === 'image') {
                            $request->validate([
                                "blocks.{$key}_file" => ['image', 'mimes:jpeg,png,webp', 'max:5120'],
                            ], [], ["blocks.{$key}_file" => 'afbeelding']);
                            $name = $file->hashName();
                            $file->move(public_path('assets/img/landing'), $name);
                            $stringValue = 'assets/img/landing/' . $name;
                        } else {
                            $request->validate([
                                "blocks.{$key}_file" => ['file', 'mimes:mp4,mov,webm', 'max:51200'],
                            ], [], ["blocks.{$key}_file" => 'video']);
                            $name = $file->hashName();
                            $file->move(public_path('assets/video'), $name);
                            $stringValue = 'assets/video/' . $name;
                        }
                    } else {
                        $stringValue = $value === null ? null : (string) $value;
                    }
                }

                ContentBlock::updateOrCreate(
                    ['page' => $page, 'section' => $section, 'block_key' => $key],
                    [
                        'type' => $blockSchema['type'],
                        'value' => $stringValue,
                        'json_value' => $jsonValue,
                    ]
                );

                $saved[$key] = $jsonValue !== null ? $jsonValue : $stringValue;
            }
        });

        Cms::bust();

        return response()->json(['message' => 'Opgeslagen.', 'saved' => $saved]);
    }

    /**
     * Save the design settings (content_meta key 'design').
     */
    public function updateDesign(Request $request): \Illuminate\Http\JsonResponse
    {
        $groups = $request->validate(['design' => ['required', 'array']]);

        $clean = [];

        foreach ($groups['design'] as $groupName => $fields) {
            if (is_array($fields)) {
                $clean[$groupName] = collect($fields)
                    ->reject(fn ($v, $k) => str_ends_with((string) $k, '_hex'))
                    ->all();
            }
        }

        ContentMeta::updateOrCreate(
            ['meta_key' => 'design'],
            ['meta_value' => json_encode($clean)]
        );

        Cms::bust();

        return response()->json(['message' => 'Ontwerpinstellingen opgeslagen.']);
    }

    /**
     * Turn posted json input into a clean list of items, keeping only
     * configured fields and casting boolean fields.
     */
    private function normalizeJson(Request $request, string $blockKey, mixed $value, array $blockSchema): array
    {
        return $this->normalizeItems($request, 'blocks.' . $blockKey, $value, $blockSchema['fields'] ?? []);
    }

    /**
     * Recursively normalize a list of items for a json/nested block field.
     * $namePrefix is the dot-notation input prefix (used for image uploads).
     */
    private function normalizeItems(Request $request, string $namePrefix, mixed $value, array $fields): array
    {
        if (! is_array($value)) {
            return [];
        }

        $items = [];

        foreach ($value as $index => $item) {
            if (! is_array($item)) {
                continue;
            }

            $clean = [];

            foreach ($fields as $field) {
                $fieldKey = $field['key'];
                $fieldType = $field['type'] ?? 'text';

                if (! array_key_exists($fieldKey, $item)) {
                    $clean[$fieldKey] = null;
                    continue;
                }

                if ($fieldType === 'boolean') {
                    $clean[$fieldKey] = filter_var($item[$fieldKey], FILTER_VALIDATE_BOOLEAN);
                    continue;
                }

                if ($fieldType === 'image') {
                    $file = $request->file("{$namePrefix}.{$index}.{$fieldKey}_file");

                    if ($file) {
                        $request->validate([
                            "{$namePrefix}.{$index}.{$fieldKey}_file" => ['image', 'mimes:jpeg,png,webp', 'max:5120'],
                        ], [], ["{$namePrefix}.{$index}.{$fieldKey}_file" => 'afbeelding']);

                        $name = $file->hashName();
                        $file->move(public_path('assets/img/landing'), $name);
                        $clean[$fieldKey] = 'assets/img/landing/' . $name;
                        continue;
                    }

                    $clean[$fieldKey] = (string) $item[$fieldKey];
                    continue;
                }

                if ($fieldType === 'nested') {
                    $clean[$fieldKey] = $this->normalizeItems(
                        $request,
                        "{$namePrefix}.{$index}.{$fieldKey}",
                        (array) $item[$fieldKey],
                        $field['fields'] ?? []
                    );
                    continue;
                }

                $clean[$fieldKey] = (string) $item[$fieldKey];
            }

            $items[] = $clean;
        }

        return $items;
    }

    /**
     * Progressive ajax upload for images/videos used inside the section editor.
     * Returns a stored path (e.g. assets/video/NAME or assets/img/landing/NAME).
     */
    public function uploadMedia(Request $request): \Illuminate\Http\JsonResponse
    {
        $data = $request->validate([
            'file' => ['required', 'file', 'mimes:jpeg,png,webp,mp4,mov,webm', 'max:51200'],
        ]);

        $file = $data['file'];
        $name = $file->hashName();
        $ext = strtolower($file->getClientOriginalExtension());

        if (in_array($ext, ['mp4', 'mov', 'webm'], true)) {
            $file->move(public_path('assets/video'), $name);
            $path = 'assets/video/' . $name;
        } else {
            $file->move(public_path('assets/img/landing'), $name);
            $path = 'assets/img/landing/' . $name;
        }

        return response()->json(['path' => $path]);
    }
}

