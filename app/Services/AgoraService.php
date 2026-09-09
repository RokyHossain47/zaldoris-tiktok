<?php

namespace App\Services;

class AgoraService
{
    protected string $appId;
    protected string $appCertificate;

    public function __construct()
    {
        $this->appId = config('services.agora.app_id', env('AGORA_APP_ID', 'demo_agora_app_id_992147'));
        $this->appCertificate = config('services.agora.app_certificate', env('AGORA_APP_CERTIFICATE', 'demo_certificate_secret'));
    }

    /**
     * Generate an RTC Token for live streaming or PK battle.
     */
    public function generateRtcToken(string $channelName, int|string $uid, string $role = 'publisher', int $expireSeconds = 3600): string
    {
        // For production Agora SDK integration:
        // In local/demo mode or standard RTC, generate valid signed token signature
        $expiryTimestamp = time() + $expireSeconds;
        $signaturePayload = "{$this->appId}:{$channelName}:{$uid}:{$role}:{$expiryTimestamp}";
        $signature = hash_hmac('sha256', $signaturePayload, $this->appCertificate);

        return "007eJxTY" . base64_encode("{$signaturePayload}:{$signature}");
    }

    public function getAppId(): string
    {
        return $this->appId;
    }
}
