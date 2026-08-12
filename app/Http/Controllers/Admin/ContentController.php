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
            'title' => 'Home-page - ' . $sectionConfig['label'],
            'page' => $page,
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
                    $jsonValue = $this->normalizeJson($value, $blockSchema);
                } else {
                    $file = $request->file("blocks.{$key}_file");

                    if (! $file && $value instanceof UploadedFile) {
                        $file = $value;
                    }

                    if ($blockSchema['type'] === 'image' && $file) {
                        $request->validate([
                            "blocks.{$key}_file" => ['image', 'mimes:jpeg,png,webp', 'max:5120'],
                        ], [], ["blocks.{$key}_file" => 'afbeelding']);

                        $name = $file->hashName();
                        $file->move(public_path('assets/img/landing'), $name);
                        $stringValue = 'assets/img/landing/' . $name;
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
    private function normalizeJson(mixed $value, array $blockSchema): array
    {
        if (! is_array($value)) {
            return [];
        }

        $fields = $blockSchema['fields'] ?? [];
        $items = [];

        foreach (array_values($value) as $item) {
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

                $clean[$fieldKey] = $fieldType === 'boolean'
                    ? filter_var($item[$fieldKey], FILTER_VALIDATE_BOOLEAN)
                    : (string) $item[$fieldKey];
            }

            $items[] = $clean;
        }

        return $items;
    }
}
