<?php

namespace App\Http\Controllers\Admin\Chat;

use App\Http\Controllers\Controller;
use App\Models\ChatFaq;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FaqController extends Controller
{
    public function index(): View
    {
        return view('admin.chat.faqs.index');
    }

    public function data(Request $request): JsonResponse
    {
        $query = ChatFaq::query();

        if ($search = $request->string('search')->trim()->toString()) {
            $query->where(function ($q) use ($search) {
                $q->where('question', 'like', "%{$search}%")
                    ->orWhere('answer', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->string('status')->toString() === 'active');
        }

        $perPage = min((int) $request->input('per_page', 15), 50);

        $paginator = $query
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate($perPage);

        return response()->json([
            'data' => $paginator->items(),
            'pagination' => [
                'current' => $paginator->currentPage(),
                'last' => $paginator->lastPage(),
                'total' => $paginator->total(),
                'per_page' => $paginator->perPage(),
            ],
            'counts' => [
                'active' => ChatFaq::where('is_active', true)->count(),
                'total' => ChatFaq::count(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'question' => ['required', 'string', 'max:500'],
            'answer' => ['required', 'string', 'max:5000'],
            'category' => ['nullable', 'string', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ], [
            'question.required' => 'Vul een vraag in.',
            'answer.required' => 'Vul een antwoord in.',
        ]);

        $faq = ChatFaq::create([
            'question' => $validated['question'],
            'answer' => $validated['answer'],
            'category' => $validated['category'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);

        dispatch(function () use ($faq) {
            app(\App\Services\Ai\Features\FaqEmbedder::class)->embedFaq($faq->fresh());
        })->afterResponse();

        return response()->json(['message' => 'Vraag toegevoegd.', 'faq' => $faq], 201);
    }

    public function show(ChatFaq $faq): JsonResponse
    {
        return response()->json(['faq' => $faq]);
    }

    public function update(Request $request, ChatFaq $faq): JsonResponse
    {
        $validated = $request->validate([
            'question' => ['required', 'string', 'max:500'],
            'answer' => ['required', 'string', 'max:5000'],
            'category' => ['nullable', 'string', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ], [
            'question.required' => 'Vul een vraag in.',
            'answer.required' => 'Vul een antwoord in.',
        ]);

        $faq->update($validated);

        dispatch(function () use ($faq) {
            app(\App\Services\Ai\Features\FaqEmbedder::class)->embedFaq($faq->fresh());
        })->afterResponse();

        return response()->json(['message' => 'Vraag bijgewerkt.', 'faq' => $faq->fresh()]);
    }

    public function destroy(ChatFaq $faq): JsonResponse
    {
        $faq->delete();

        return response()->json(['message' => 'Vraag verwijderd.']);
    }

    public function toggle(ChatFaq $faq): JsonResponse
    {
        $faq->update(['is_active' => ! $faq->is_active]);

        return response()->json([
            'message' => $faq->is_active ? 'Vraag geactiveerd.' : 'Vraag gedeactiveerd.',
            'is_active' => $faq->is_active,
        ]);
    }
}
