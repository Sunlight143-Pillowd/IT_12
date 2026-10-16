<?php

namespace App\Support;

final class PhilippineVat
{
    public const RATE_PERCENT = 12;

    /**
     * @return array{net: float, vat: float, total: float}
     */
    public static function breakdown(float $vatInclusiveTotal): array
    {
        $total = round($vatInclusiveTotal, 2);
        $vat = round($total * self::RATE_PERCENT / (100 + self::RATE_PERCENT), 2);

        return [
            'net' => round($total - $vat, 2),
            'vat' => $vat,
            'total' => $total,
        ];
    }
}
