<?php

namespace App\Services\Ai\Features\ChatTools;

/**
 * Eén stuk gereedschap voor de chat-agent.
 * De agent (LLM) kiest zelf welke tools hij aanroept — er is
 * geen keyword/regex-classificatie meer in PHP.
 */
interface ChatTool
{
    /**
     * OpenAI function-calling definitie.
     *
     * @return array{type: string, function: array{name: string, description: string, parameters: array<string, mixed>}}
     */
    public function definition(): array;

    /**
     * Voer de tool uit. Geeft ALTIJD compacte tekst terug voor de LLM
     * (nooit exceptions naar de agent laten lekken).
     *
     * @param  array<string, mixed>  $args
     */
    public function execute(array $args): string;
}
