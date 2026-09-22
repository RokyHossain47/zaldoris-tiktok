<?php

namespace App\Services;

class TaxService
{
    const HST_RATE = 0.13; // 13% Canadian Harmonized Sales Tax (SRS Page 5)
    const PLATFORM_PRODUCT_COMMISSION_RATE = 0.07; // 7% product sales commission (SRS Page 4)
    const CREATOR_GIFT_REVENUE_SHARE = 0.60; // 60% creator cut on gifts (SRS Page 4)
    const CREATOR_SUBSCRIPTION_PRICE = 7.99; // $7.99/month (SRS Page 4)

    /**
     * Calculate 13% HST tax on a given amount.
     */
    public static function calculateHst(float $amount): float
    {
        return round($amount * self::HST_RATE, 2);
    }

    /**
     * Calculate mandatory shipping insurance fee for orders over $50 (SRS #8).
     * Split: 50% seller, 50% buyer. Cost $0.50 - $1.00 per $100 declared value.
     */
    public static function calculateShippingInsurance(float $subtotal): float
    {
        if ($subtotal < 50.00) {
            return 0.00;
        }

        // $1.00 per $100 of value, split in half for buyer ($0.50 per $100)
        $rate = 0.005; // 0.5% for buyer share
        return round(max(0.50, $subtotal * $rate), 2);
    }

    /**
     * Calculate breakdown for an e-commerce / auction order.
     */
    public static function calculateOrderTotals(float $subtotal, float $shippingFee = 10.00): array
    {
        $insuranceFee = self::calculateShippingInsurance($subtotal);
        $taxableAmount = $subtotal + $shippingFee + $insuranceFee;
        $hstTax = self::calculateHst($taxableAmount);
        $totalAmount = round($taxableAmount + $hstTax, 2);
        $platformCommission = round($subtotal * self::PLATFORM_PRODUCT_COMMISSION_RATE, 2);
        $sellerPayout = round($subtotal - $platformCommission, 2);
        $requiresSignature = $subtotal >= 150.00; // SRS #8

        return [
            'subtotal' => $subtotal,
            'shipping_fee' => $shippingFee,
            'insurance_fee' => $insuranceFee,
            'taxable_amount' => $taxableAmount,
            'hst_tax' => $hstTax,
            'hst_rate_percent' => 13,
            'total_amount' => $totalAmount,
            'platform_commission' => $platformCommission,
            'seller_payout' => $sellerPayout,
            'requires_signature' => $requiresSignature,
        ];
    }
}
