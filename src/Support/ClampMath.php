<?php

declare(strict_types=1);

namespace RalfHortt\WpCliCss\Support;

final class ClampMath
{
    public static function parsePixelValue(string $value): ?float
    {
        $value = trim($value);

        if (preg_match('/^(-?\d+(?:\.\d+)?)(?:px)?$/i', $value, $matches)) {
            return (float) $matches[1];
        }

        return null;
    }

    public static function pxToRem(float $px, float $baseSize = 16.0): float
    {
        return round(($px / $baseSize) * 100) / 100;
    }

    public static function calculateClamp(
        float $minViewport,
        float $maxViewport,
        float $minValue,
        float $maxValue,
        string $unit = 'rem',
        float $baseSize = 16.0,
    ): string {
        if ($unit === 'rem') {
            $minValue = self::pxToRem($minValue, $baseSize);
            $maxValue = self::pxToRem($maxValue, $baseSize);
        }

        $slope = ($maxValue - $minValue) / ($maxViewport - $minViewport);
        $yIntercept = $minValue - $slope * $minViewport;
        $preferredValue = number_format($yIntercept, 4, '.', '') . $unit . ' + ' . number_format($slope * 100, 4, '.', '') . 'vw';

        return sprintf('clamp(%s%s, %s, %s%s)', $minValue, $unit, $preferredValue, $maxValue, $unit);
    }
}
