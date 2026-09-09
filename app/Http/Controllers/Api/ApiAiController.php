<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AiModerationService;

class ApiAiController extends Controller
{
    /**
     * AI Content Moderation Endpoint (SRS Page 5).
     */
    public function moderate(Request $request)
    {
        $request->validate([
            'text' => 'required|string',
            'type' => 'nullable|string',
        ]);

        $aiService = new AiModerationService();
        $res = $aiService->moderateText($request->text, $request->type ?? 'api_check', $request->user() ? $request->user()->id : null);

        return response()->json($res);
    }

    /**
     * AI Personal Shopping Assistant Recommendations (SRS Page 5).
     */
    public function shoppingAssistant(Request $request)
    {
        $query = $request->query('query');
        $category = $request->query('category');

        $aiService = new AiModerationService();
        $recommendations = $aiService->getShoppingRecommendations($query, $category, 10);

        return response()->json([
            'success' => true,
            'recommendations' => $recommendations,
        ]);
    }

    /**
     * Voice-Activated Search Endpoint (SRS Page 5).
     */
    public function voiceSearch(Request $request)
    {
        $request->validate([
            'voice_transcript' => 'required|string',
        ]);

        $aiService = new AiModerationService();
        $parsed = $aiService->parseVoiceSearch($request->voice_transcript);
        $results = $aiService->getShoppingRecommendations($parsed['parsed_query'], $parsed['detected_category']);

        return response()->json([
            'success' => true,
            'voice_analysis' => $parsed,
            'matched_products' => $results,
        ]);
    }
}
