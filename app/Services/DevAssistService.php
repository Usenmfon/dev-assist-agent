<?php

namespace App\Services;

use App\Neuron\DevAssistAgent;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use NeuronAI\Chat\Messages\UserMessage;

class DevAssistService
{
    public function detectIntent(string $message): string
    {
        $msg = strtolower($message);

        return match (true) {
            str_contains($msg, 'explain') => 'explain_code',
            str_contains($msg, 'generate') => 'generate_code',
            str_contains($msg, 'fix') => 'fix_code',
            default => 'general',
        };
    }

    public function processMessage(string $intent, string $message): string
    {
        $prefixed = match ($intent) {
            'explain_code' => "[EXPLAIN]\n{$message}",
            'generate_code' => "[GENERATE]\n{$message}",
            'fix_code' => "[FIX]\n{$message}",
            default => $message,
        };

        try {
            $agent = DevAssistAgent::make();
            $result = $agent->chat(new UserMessage($prefixed));

            if (is_object($result)) {
                if (method_exists($result, 'getContent')) {
                    return $result->getContent();
                }

                if (method_exists($result, 'content')) {
                    return $result->content();
                }

                if (method_exists($result, 'toArray')) {
                    $array = $result->toArray();
                    if (isset($array['content'])) {
                        return $array['content'];
                    }
                }
            }

            return (string) $result;
        } catch (\Throwable $e) {
            Log::error('Agent error: '.$e->getMessage());

            return 'Sorry — the Dev Assist agent failed to respond. Try again later.';
        }
    }

    public function sendToTelex(string $channelId, string $response): void
    {
        try {
            $telexResponse = Http::post(
                'https://api.telex.im/agent-message',
                [
                    'channel_id' => $channelId,
                    'text' => $response,
                ]
            );

            if ($telexResponse->successful()) {
                Log::info('✅ Message sent to Telex', [
                    'status' => $telexResponse->status(),
                    'body' => $telexResponse->json(),
                ]);
            } elseif ($telexResponse->clientError()) {
                Log::warning('⚠️ Client error when sending to Telex', [
                    'status' => $telexResponse->status(),
                    'body' => $telexResponse->body(),
                ]);
            } elseif ($telexResponse->serverError()) {
                Log::error('❌ Server error when sending to Telex', [
                    'status' => $telexResponse->status(),
                    'body' => $telexResponse->body(),
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('💥 Telex send error: '.$e->getMessage());
        }
    }
}
