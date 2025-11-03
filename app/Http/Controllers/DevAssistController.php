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
        $validated = $request->validate([
            'channel_id' => 'required|string',
            'user_id' => 'required|string',
            'message' => 'required|string',
        ]);

        $intent = $service->detectIntent($validated['message']);
        $aiResponse = $service->processMessage($intent, $validated['message']);

        DevAssist::create([
            'channel_id' => $validated['channel_id'],
            'user_id' => $validated['user_id'],
            'message' => $validated['message'],
            'response' => $aiResponse,
            'intent' => $intent,
        ]);

        $service->sendToTelex($validated['channel_id'], $aiResponse);

        return response()->json(['status' => 'success', 'response' => $aiResponse]);
    }

    public function logs()
    {
        return DevAssist::latest()->paginate(10);
    }
}
