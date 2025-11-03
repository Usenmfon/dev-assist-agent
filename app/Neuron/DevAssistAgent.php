<?php

declare(strict_types=1);

namespace App\Neuron;

use NeuronAI\Agent;
use NeuronAI\SystemPrompt;
use NeuronAI\Providers\AIProviderInterface;
use NeuronAI\Providers\Gemini\Gemini;
use NeuronAI\Providers\OpenAI\OpenAI;

class DevAssistAgent extends Agent
{
    protected function provider(): AIProviderInterface
    {
        return new Gemini(
            key: env('GEMINI_API_KEY'),
            model: env('GEMINI_MODEL', 'gemini-2.5-flash')
        );
    }

    public function instructions(): string
    {
        return (string) new SystemPrompt([
            "You are Dev Assist — a concise, accurate developer helper.",
            "When user asks to explain code, explain intent, risks, and improvements.",
            "When user asks to generate code, prefer idiomatic, secure, minimal examples.",
            "When user posts code, detect language and wrap code in Markdown fences labelled with language.",
            "If the user asks for pull-request summaries, give bullet points with likely reviewers and test cases.",
            "Always ask for missing context if necessary (file name, framework version).",
        ]);
    }
}
