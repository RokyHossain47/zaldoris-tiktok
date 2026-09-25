<?php

namespace App\Services;

use App\Models\Setting;

class ShippingService
{
    /**
     * Default fallback shipping methods.
     */
    public static function defaultMethods(): array
    {
        return [
            [
                'id' => 'standard_delivery',
                'name' => 'Standard Ground Delivery',
                'cost' => 0.00,
                'delivery_time' => '3 - 5 Business Days',
                'description' => 'Reliable tracked parcel delivery across all domestic zones.',
                'is_active' => true,
                'is_default' => true,
            ],
            [
                'id' => 'express_courier',
                'name' => 'Express Priority Courier',
                'cost' => 15.00,
                'delivery_time' => '1 - 2 Business Days',
                'description' => 'Fast air dispatch with real-time tracking & SMS arrival alerts.',
                'is_active' => true,
                'is_default' => false,
            ],
            [
                'id' => 'overnight_vip',
                'name' => 'Overnight VIP Expedited',
                'cost' => 25.00,
                'delivery_time' => 'Next-Day Delivery',
                'description' => 'Guaranteed next morning delivery with direct signature verification.',
                'is_active' => true,
                'is_default' => false,
            ],
        ];
    }

    /**
     * Get all configured shipping methods.
     */
    public static function getMethods(): array
    {
        $raw = Setting::get('shipping_methods');
        if (empty($raw)) {
            return self::defaultMethods();
        }

        $decoded = is_string($raw) ? json_decode($raw, true) : $raw;
        return is_array($decoded) && count($decoded) > 0 ? $decoded : self::defaultMethods();
    }

    /**
     * Get only active shipping methods.
     */
    public static function getActiveMethods(): array
    {
        $methods = self::getMethods();
        $active = array_values(array_filter($methods, fn($m) => !empty($m['is_active'])));
        return count($active) > 0 ? $active : self::defaultMethods();
    }

    /**
     * Find a shipping method by ID.
     */
    public static function findMethod(?string $id): ?array
    {
        if (empty($id)) {
            return self::getDefaultMethod();
        }

        foreach (self::getMethods() as $m) {
            if (($m['id'] ?? '') === $id) {
                return $m;
            }
        }

        return self::getDefaultMethod();
    }

    /**
     * Get the default shipping method.
     */
    public static function getDefaultMethod(): ?array
    {
        $active = self::getActiveMethods();
        foreach ($active as $m) {
            if (!empty($m['is_default'])) {
                return $m;
            }
        }
        return $active[0] ?? null;
    }
}
