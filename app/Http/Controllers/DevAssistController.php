<?php

namespace App\Http\Controllers;

use App\Models\DevAssist;
use Illuminate\Http\Request;
use App\Services\DevAssistService;
use Illuminate\Support\Facades\Log;

class DevAssistController extends Controller
{
    public function handleWebhook(Request $request, DevAssistService $service)
    {
        if ($request->isMethod('get')) {
            return response()->json([
                'status' => 'success',
                'data' => [
                    'message' => 'Dev Assist A2A endpoint is active and ready 🚀',
                ],
            ]);
        }

        $validated = $request->validate([
            'message' => 'required|string',
        ]);

        $intent = $service->detectIntent($validated['message']);
        $aiResponse = $service->processMessage($intent, $validated['message']);

        DevAssist::create([
            'message' => $validated['message'],
            'response' => $aiResponse,
            'intent' => $intent,
        ]);

        // $service->sendToTelex($validated['channel_id'], $aiResponse);

        return response()->json([
            'status' => 'success',
            'content' => [
                'text' => $aiResponse,
            ],
        ]);
    }

    public function logs()
    {
        return DevAssist::latest()->paginate(10);
    }
}
