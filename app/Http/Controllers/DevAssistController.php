<?php

namespace App\Http\Controllers;

use App\Models\DevAssist;
use App\Services\DevAssistService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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

        $taskId = $message['taskId'] ?? Str::uuid()->toString();
        $contextId = Str::uuid()->toString();
        $messageId = Str::uuid()->toString();
        $artifactMsg = Str::uuid()->toString();
        $artifactTool = Str::uuid()->toString();
        $id = 'dev_assist_node';

        return response()->json([
            'jsonrpc' => '2.0',
            'id' => $id,
            'result' => [
                'id' => $taskId,
                'contextId' => $contextId,
                'status' => [
                    'state' => 'completed',
                    'timestamp' => now()->toISOString(),
                    'message' => [
                        'messageId' => $messageId,
                        'role' => 'agent',
                        'kind' => 'message',
                        'parts' => [
                            [
                                'kind' => 'text',
                                'text' => $aiResponse,
                            ],
                        ],
                    ],
                ],

                'artifacts' => [
                    [
                        'artifactId' => $artifactMsg,
                        'name' => 'newsAgentResponse',
                        'parts' => [
                            [
                                'kind' => 'text',
                                'text' => $aiResponse,
                            ],
                        ],
                    ],
                    [
                        'artifactId' => $artifactTool,
                        'name' => 'ToolResults',
                        'parts' => [
                            [
                                'kind' => 'data',
                                'data' => [
                                    'type' => 'tool-result',
                                    'runId' => Str::uuid()->toString(),
                                    'from' => 'AGENT',
                                    'payload' => [
                                        'args' => [
                                            'text' => $message,
                                        ],
                                        'toolName' => 'NewsAgent',
                                        'result' => [
                                            'success' => true,
                                            'responseLength' => strlen($aiResponse),
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'history' => [$message],
                'kind' => 'message',
            ],
        ]);

    }

    public function logs()
    {
        return DevAssist::latest()->paginate(10);
    }
}
