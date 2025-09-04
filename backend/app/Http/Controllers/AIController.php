<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AIService;

class AIController extends Controller
{
    protected $ai_service;

    public function __construct(AIService $ai_service){
        $this->ai_service = $ai_service;
    }

    public function generate_description(Request $request){
        $request->validate(['name' => 'required|string|max:255',]);

        $name = $request->input('name');
        $html = app(AIService::class)->generate_description($name);

        return response()->json(['description' => $html]);
    }

    public function chat(Request $request){
        $request->validate([
            'messages' => 'required|array',
            'messages.*.role' => 'required|string|in:user,model',
            'messages.*.text' => 'required|string',
        ]);

        $messages = $request->input('messages');
        $reply = $this->ai_service->chat($messages);

        return response()->json([
            'reply' => $reply
        ]);
    }
}
