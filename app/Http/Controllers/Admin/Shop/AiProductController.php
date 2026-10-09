<?php

namespace App\Http\Controllers\Admin\Shop;

use App\Http\Controllers\Controller;
use App\Services\Ai\AiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AiProductController extends Controller
{
    /**
     * Generate an AI-powered product description with live web search.
     */
    public function generateDescription(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title'                   => 'required|string|max:255',
            'brand'                   => 'nullable|string|max:100',
            'sku'                     => 'nullable|string|max:100',
            'category_name'           => 'nullable|string|max:100',
            'price'                   => 'nullable|numeric',
            // Cap the item count: each feature grows the prompt, and an
            // unbounded array turns this paid-per-token call into a cost
            // amplifier (plus a prompt-injection carrier).
            'features'                => 'nullable|array|max:20',
            'features.*'              => 'nullable|string|max:255',
            'additional_instructions' => 'nullable|string|max:500',
            'enable_search'           => 'nullable|boolean',
        ]);

        try {
            $result = AiService::generateProductDescription($validated, [
                'enable_search'           => $request->boolean('enable_search', true),
                'additional_instructions' => $request->input('additional_instructions'),
            ]);

            return response()->json([
                'success'        => true,
                'description'    => $result['description'],
                'search_results' => $result['search_results'],
                'search_count'   => $result['search_count'],
            ]);
        } catch (\Throwable $e) {
            // Never forward provider internals (key names, quota/billing
            // state, upstream bodies) to the browser; log them instead.
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'De AI-beschrijving kon niet worden gegenereerd. Probeer het later opnieuw.',
            ], 422);
        }
    }
}
