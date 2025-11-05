<?php

namespace App\Http\Controllers;

use App\Models\DevAssist;
use App\Services\DevAssistService;
use Illuminate\Http\Request;

class DevAssistController extends Controller
{
    public function handleWebhook(Request $request, DevAssistService $service)
    {
        if ($request->isMethod('get')) {
            return response()->json([
                'status' => 'success',
                'response' => 'Dev Assist A2A endpoint is active and ready 🚀',
            ]);
        }

        $payload = $request->all();

        // Telex sends messages inside params.message.parts[0].text
        $message = data_get($payload, 'params.message.parts.0.text');
        $channelId = data_get($payload, 'params.channel_id', 'unknown-channel');
        $userId = data_get($payload, 'params.user_id', 'unknown-user');

        if (! $message) {
            return response()->json([
                'jsonrpc' => '2.0',
                'id' => 'dev_assist_node',
                'error' => [
                    'code' => -32602,
                    'message' => 'Invalid A2A request — missing message text.',
                ],
            ], 400);
        }

        $intent = $service->detectIntent($message);
        $aiResponse = $service->processMessage($intent, $message);

        // Save or send response...
        $service->sendToTelex($channelId, $aiResponse);

        return response()->json([
            'jsonrpc' => '2.0',
            'id' => 'dev_assist_node',
            'result' => [
                'messages' => [
                    [
                        'kind' => 'message',
                        'role' => 'assistant',
                        'parts' => [
                            [
                                'kind' => 'text',
                                'text' => $message,
                            ],
                        ],
                        'artifacts' => [
                            [
                                'type' => 'code',
                                'language' => 'php',
                                'title' => 'Optimized Code',
                                'content' => $aiResponse,
                            ],
                        ],
                    ],
                ],
            ],
        ]);

    }

    public function logs()
    {
        return DevAssist::latest()->paginate(10);
    }
}
