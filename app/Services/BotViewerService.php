<?php

namespace App\Services;

use App\Models\Stream;
use App\Models\StreamMessage;
use App\Models\User;
use App\Models\Notification;
use App\Models\CoinTransaction;

class BotViewerService
{
    protected array $encouragingMessages = [
        "What's coming up next? 🔥",
        "Love that product! Showing great detail ✨",
        "How is the sizing on this item?",
        "Can you show the back angle please?",
        "Just shared the stream with my friends!",
        "Great quality on stream today 👏",
        "Following your shop right now!",
        "Are there more color options available?",
    ];

    protected array $botUsernames = [
        "Alex_Trends", "SophiaLive", "SneakerKing_99", "NovaStyle", "FashionFinds_CA",
        "Collector_Pro", "Emma_Shop", "HyperBeast_23", "UrbanChic", "VibeHunter"
    ];

    /**
     * Trigger cold start warm-up for new seller streams (SRS #11).
     */
    public function warmUpStream(Stream $stream): array
    {
        // 1. Send bot chat message
        $randomMsg = $this->encouragingMessages[array_rand($this->encouragingMessages)];
        $randomBot = $this->botUsernames[array_rand($this->botUsernames)];

        $chat = StreamMessage::create([
            'stream_id' => $stream->id,
            'user_id' => null,
            'username_display' => $randomBot,
            'message' => $randomMsg,
            'is_bot' => true,
            'message_type' => 'chat',
        ]);

        // 2. Increment simulated viewer counter
        $stream->viewer_count = max(5, $stream->viewer_count + rand(2, 6));
        $stream->save();

        return [
            'bot_username' => $randomBot,
            'message' => $randomMsg,
            'viewer_count' => $stream->viewer_count,
        ];
    }

    /**
     * Award 50 bonus coins to top engaged buyers joining new seller sessions (SRS #11).
     */
    public function awardWarmUpBonusCoins(User $user, Stream $stream): array
    {
        // Only if seller is new (< 5 streams)
        $sellerStreamCount = Stream::where('host_id', $stream->host_id)->count();

        if ($sellerStreamCount <= 5) {
            $alreadyAwarded = CoinTransaction::where('user_id', $user->id)
                ->where('type', 'bonus')
                ->where('reference_id', "stream_warmup_{$stream->id}")
                ->exists();

            if (!$alreadyAwarded) {
                $user->coin_balance += 50;
                $user->save();

                CoinTransaction::create([
                    'user_id' => $user->id,
                    'type' => 'bonus',
                    'amount_coins' => 50,
                    'amount_usd' => 0.00,
                    'reference_id' => "stream_warmup_{$stream->id}",
                    'description' => "50 Bonus Coins for supporting new creator/seller session #{$stream->id}",
                ]);

                Notification::create([
                    'user_id' => $user->id,
                    'title' => '🎉 50 Bonus Coins Received!',
                    'message' => "You earned 50 bonus coins for joining and warming up {$stream->host->name}'s live session!",
                    'type' => 'bonus_coins',
                    'action_url' => '/wallet/coins',
                ]);

                return ['awarded' => true, 'bonus_coins' => 50];
            }
        }

        return ['awarded' => false, 'bonus_coins' => 0];
    }
}
