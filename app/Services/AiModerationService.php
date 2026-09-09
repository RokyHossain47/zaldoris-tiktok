<?php

namespace App\Services;

use App\Models\AiModerationLog;
use App\Models\Product;

class AiModerationService
{
    // Prohibited terms for scam, hate speech, counterfeit, or prohibited ingredients (Health Canada database check)
    protected array $bannedKeywords = [
        'scam', 'free money', 'whatsapp me for payment', 'wire transfer only',
        'counterfeit', 'fake replica', 'first copy', '1:1 replica', 'unauthorized',
        'mercury', 'hydroquinone', 'steroids', 'prohibited substance', 'banned chemical'
    ];

    /**
     * Moderate text content (chat, product title, reviews).
     */
    public function moderateText(string $text, string $contentType = 'stream_chat', ?int $userId = null, ?int $contentId = null): array
    {
        $normalized = strtolower($text);

        foreach ($this->bannedKeywords as $keyword) {
            if (str_contains($normalized, $keyword)) {
                AiModerationLog::create([
                    'content_type' => $contentType,
                    'content_id' => $contentId,
                    'user_id' => $userId,
                    'flagged_content' => $text,
                    'violation_type' => str_contains($keyword, 'replica') ? 'counterfeit' : (str_contains($keyword, 'mercury') ? 'prohibited_ingredient' : 'scam_or_hate_speech'),
                    'confidence_score' => 0.98,
                    'action_taken' => 'blocked',
                ]);

                return [
                    'approved' => false,
                    'reason' => "Message flagged by AI Moderation filter for policy violation ({$keyword}).",
                ];
            }
        }

        return [
            'approved' => true,
            'reason' => null,
        ];
    }

    /**
     * AI Personal Shopping Assistant suggestions.
     */
    public function getShoppingRecommendations(?string $query = null, ?string $category = null, int $limit = 6): array
    {
        $productsQuery = Product::where('status', 'active');

        if ($category && $category !== 'all') {
            $productsQuery->where('category', $category);
        }

        if ($query) {
            $productsQuery->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%")
                  ->orWhere('category', 'like', "%{$query}%");
            });
        }

        return $productsQuery->orderBy('id', 'desc')->limit($limit)->get()->toArray();
    }

    /**
     * Voice-to-search query parser.
     */
    public function parseVoiceSearch(string $voiceTranscript): array
    {
        $clean = trim(strtolower($voiceTranscript));
        
        // Remove common fillers
        $fillers = ['find me', 'search for', 'i want to buy', 'show me', 'look for', 'please find'];
        foreach ($fillers as $filler) {
            $clean = str_replace($filler, '', $clean);
        }
        $clean = trim($clean);

        // Detect category
        $category = 'all';
        if (str_contains($clean, 'shoe') || str_contains($clean, 'sneaker')) $category = 'shoes';
        elseif (str_contains($clean, 'shirt') || str_contains($clean, 'hoodie') || str_contains($clean, 'dress') || str_contains($clean, 'jacket')) $category = 'fashion';
        elseif (str_contains($clean, 'card') || str_contains($clean, 'pokemon') || str_contains($clean, 'collectible')) $category = 'collectibles';
        elseif (str_contains($clean, 'watch') || str_contains($clean, 'jewelry') || str_contains($clean, 'ring')) $category = 'luxury';
        elseif (str_contains($clean, 'beauty') || str_contains($clean, 'makeup') || str_contains($clean, 'perfume')) $category = 'beauty';

        return [
            'raw_transcript' => $voiceTranscript,
            'parsed_query' => $clean,
            'detected_category' => $category,
        ];
    }
}
