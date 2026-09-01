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
            'features'                => 'nullable|array',
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
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
