<?php

namespace App\Services\Ai\Features;

use App\Models\ChatConversation;
use App\Services\Ai\AiService;

/**
 * Antwoorden voor de website-chat — dunne laag boven de ChatAgent.
 *
 * Al het begrip (intenties, producten, vergelijkingen, geheugen) zit bij
 * de agent (LLM + tools). Hier blijft alleen over: historie + de korte
 * ticket-/bedank-bevestigingen voor handover en offline.
 */
class ChatAnswerGenerator
{
    public function __construct(protected ?ChatAgent $agent = null)
    {
        $this->agent ??= new ChatAgent();
    }

    /**
     * @return array{text: string, handoff_offer: bool, products: array<int, array<string, mixed>>}
     */
    public function generate(ChatConversation $conversation, string $userText, bool $hasPhoto = false): array
    {
        return $this->agent->generate($conversation, $userText, $hasPhoto);
    }

    /**
     * Korte bevestiging door de AI zelf, in de taal van de klant.
     * De taal volgt uit het geciteerde klantbericht (geen detectiecode).
     * Valt terug op Nederlands bij een LLM-fout.
     */
    public function confirmation(ChatConversation $conversation, string $kind): string
    {
        $history = $this->history($conversation);
        $lastCustomer = '';
        foreach (array_reverse($history) as $m) {
            if ($m['role'] === 'user' && $m['content'] !== '') {
                $lastCustomer = mb_substr($m['content'], 0, 500);
                break;
            }
        }
        $instruction = $kind === 'thanks'
            ? 'Schrijf een kort bedankje (max 2 zinnen): het ticket is aangemaakt en we reageren per e-mail.'
            : 'Schrijf een korte ticketbevestiging (max 2 zinnen): het ticket is aangemaakt en een medewerker neemt het over, even geduld.';
        $userContent = $instruction;
        if ($lastCustomer !== '') {
            $userContent .= ' BELANGRIJKSTE REGEL: schrijf VOLLEDIG in DEZELFDE TAAL als dit klantbericht (letterlijk overnemen van de taal, geen uitzonderingen): "'.$lastCustomer.'"';
        }
        $messages = [
            ['role' => 'system', 'content' => 'Je bent de chatassistent van Slimme-PC. Kort, zonder Markdown, geen slotvragen, geen handoff-aanbod.'],
            ...$history,
            ['role' => 'user', 'content' => $userContent],
        ];

        try {
            return AiService::chat($messages, ['temperature' => 0.3, 'max_tokens' => 150]);
        } catch (\Throwable $e) {
            report($e);

            return $kind === 'thanks'
                ? 'Bedankt! Je ticket is aangemaakt — we reageren per e-mail.'
                : 'Ticket aangemaakt! Een medewerker neemt dit gesprek over — even geduld alsjeblieft.';
        }
    }

    /**
     * @return array<int, array{role: string, content: string}>
     */
    protected function history(ChatConversation $conversation): array
    {
        // reorder(): zie ChatAgent::history() — relatie heeft ASC ingebakken.
        return $conversation->messages()->reorder()
            ->orderByDesc('id')
            ->limit(12)
            ->get()
            ->reverse()
            ->map(fn ($m) => [
                'role' => $m->sender === 'customer' ? 'user' : 'assistant',
                'content' => (string) ($m->body ?? ''),
            ])
            ->filter(fn ($m) => $m['content'] !== '')
            ->values()
            ->all();
    }
}
