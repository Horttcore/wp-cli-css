<?php

declare(strict_types=1);

namespace RalfHortt\WpCliCss\Support;

final class ContrastMath
{
    /**
     * @param  array{r:int,g:int,b:int}  $rgb1
     * @param  array{r:int,g:int,b:int}  $rgb2
     */
    public static function calculateContrastRatio(array $rgb1, array $rgb2): float
    {
        $luminance1 = self::calculateRelativeLuminance($rgb1['r'], $rgb1['g'], $rgb1['b']);
        $luminance2 = self::calculateRelativeLuminance($rgb2['r'], $rgb2['g'], $rgb2['b']);

        $lighter = max($luminance1, $luminance2);
        $darker = min($luminance1, $luminance2);

        return ($lighter + 0.05) / ($darker + 0.05);
    }

    /**
     * @return array{AA_normal:bool,AA_large:bool,AAA_normal:bool,AAA_large:bool}
     */
    public static function getAccessibilityStatus(float $contrastRatio): array
    {
        return [
            'AA_normal' => $contrastRatio >= 4.5,
            'AA_large' => $contrastRatio >= 3.0,
            'AAA_normal' => $contrastRatio >= 7.0,
            'AAA_large' => $contrastRatio >= 4.5,
        ];
    }

    private static function calculateRelativeLuminance(int $r, int $g, int $b): float
    {
        $rsRGB = $r / 255;
        $gsRGB = $g / 255;
        $bsRGB = $b / 255;

        $rLinear = $rsRGB <= 0.03928 ? $rsRGB / 12.92 : (($rsRGB + 0.055) / 1.055) ** 2.4;
        $gLinear = $gsRGB <= 0.03928 ? $gsRGB / 12.92 : (($gsRGB + 0.055) / 1.055) ** 2.4;
        $bLinear = $bsRGB <= 0.03928 ? $bsRGB / 12.92 : (($bsRGB + 0.055) / 1.055) ** 2.4;

        return 0.2126 * $rLinear + 0.7152 * $gLinear + 0.0722 * $bLinear;
    }
}
