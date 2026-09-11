<?php

declare(strict_types=1);

namespace RalfHortt\WpCliCss\Support;

final class ColorMath
{
    /** @var array<string, string> */
    private static array $namedColors = [
        'aliceblue' => '#f0f8ff', 'antiquewhite' => '#faebd7', 'aqua' => '#00ffff',
        'aquamarine' => '#7fffd4', 'azure' => '#f0ffff', 'beige' => '#f5f5dc',
        'bisque' => '#ffe4c4', 'black' => '#000000', 'blanchedalmond' => '#ffebcd',
        'blue' => '#0000ff', 'blueviolet' => '#8a2be2', 'brown' => '#a52a2a',
        'burlywood' => '#deb887', 'cadetblue' => '#5f9ea0', 'chartreuse' => '#7fff00',
        'chocolate' => '#d2691e', 'coral' => '#ff7f50', 'cornflowerblue' => '#6495ed',
        'cornsilk' => '#fff8dc', 'crimson' => '#dc143c', 'cyan' => '#00ffff',
        'darkblue' => '#00008b', 'darkcyan' => '#008b8b', 'darkgoldenrod' => '#b8860b',
        'darkgray' => '#a9a9a9', 'darkgreen' => '#006400', 'darkkhaki' => '#bdb76b',
        'darkmagenta' => '#8b008b', 'darkolivegreen' => '#556b2f', 'darkorange' => '#ff8c00',
        'darkorchid' => '#9932cc', 'darkred' => '#8b0000', 'darksalmon' => '#e9967a',
        'darkseagreen' => '#8fbc8f', 'darkslateblue' => '#483d8b', 'darkslategray' => '#2f4f4f',
        'darkturquoise' => '#00ced1', 'darkviolet' => '#9400d3', 'deeppink' => '#ff1493',
        'deepskyblue' => '#00bfff', 'dimgray' => '#696969', 'dodgerblue' => '#1e90ff',
        'firebrick' => '#b22222', 'floralwhite' => '#fffaf0', 'forestgreen' => '#228b22',
        'fuchsia' => '#ff00ff', 'gainsboro' => '#dcdcdc', 'ghostwhite' => '#f8f8ff',
        'gold' => '#ffd700', 'goldenrod' => '#daa520', 'gray' => '#808080',
        'green' => '#008000', 'greenyellow' => '#adff2f', 'honeydew' => '#f0fff0',
        'hotpink' => '#ff69b4', 'indianred' => '#cd5c5c', 'indigo' => '#4b0082',
        'ivory' => '#fffff0', 'khaki' => '#f0e68c', 'lavender' => '#e6e6fa',
        'lavenderblush' => '#fff0f5', 'lawngreen' => '#7cfc00', 'lemonchiffon' => '#fffacd',
        'lightblue' => '#add8e6', 'lightcoral' => '#f08080', 'lightcyan' => '#e0ffff',
        'lightgoldenrodyellow' => '#fafad2', 'lightgray' => '#d3d3d3', 'lightgreen' => '#90ee90',
        'lightpink' => '#ffb6c1', 'lightsalmon' => '#ffa07a', 'lightseagreen' => '#20b2aa',
        'lightskyblue' => '#87cefa', 'lightslategray' => '#778899', 'lightsteelblue' => '#b0c4de',
        'lightyellow' => '#ffffe0', 'lime' => '#00ff00', 'limegreen' => '#32cd32',
        'linen' => '#faf0e6', 'magenta' => '#ff00ff', 'maroon' => '#800000',
        'mediumaquamarine' => '#66cdaa', 'mediumblue' => '#0000cd', 'mediumorchid' => '#ba55d3',
        'mediumpurple' => '#9370db', 'mediumseagreen' => '#3cb371', 'mediumslateblue' => '#7b68ee',
        'mediumspringgreen' => '#00fa9a', 'mediumturquoise' => '#48d1cc', 'mediumvioletred' => '#c71585',
        'midnightblue' => '#191970', 'mintcream' => '#f5fffa', 'mistyrose' => '#ffe4e1',
        'moccasin' => '#ffe4b5', 'navajowhite' => '#ffdead', 'navy' => '#000080',
        'oldlace' => '#fdf5e6', 'olive' => '#808000', 'olivedrab' => '#6b8e23',
        'orange' => '#ffa500', 'orangered' => '#ff4500', 'orchid' => '#da70d6',
        'palegoldenrod' => '#eee8aa', 'palegreen' => '#98fb98', 'paleturquoise' => '#afeeee',
        'palevioletred' => '#db7093', 'papayawhip' => '#ffefd5', 'peachpuff' => '#ffdab9',
        'peru' => '#cd853f', 'pink' => '#ffc0cb', 'plum' => '#dda0dd',
        'powderblue' => '#b0e0e6', 'purple' => '#800080', 'red' => '#ff0000',
        'rosybrown' => '#bc8f8f', 'royalblue' => '#4169e1', 'saddlebrown' => '#8b4513',
        'salmon' => '#fa8072', 'sandybrown' => '#f4a460', 'seagreen' => '#2e8b57',
        'seashell' => '#fff5ee', 'sienna' => '#a0522d', 'silver' => '#c0c0c0',
        'skyblue' => '#87ceeb', 'slateblue' => '#6a5acd', 'slategray' => '#708090',
        'snow' => '#fffafa', 'springgreen' => '#00ff7f', 'steelblue' => '#4682b4',
        'tan' => '#d2b48c', 'teal' => '#008080', 'thistle' => '#d8bfd8',
        'tomato' => '#ff6347', 'turquoise' => '#40e0d0', 'violet' => '#ee82ee',
        'wheat' => '#f5deb3', 'white' => '#ffffff', 'whitesmoke' => '#f5f5f5',
        'yellow' => '#ffff00', 'yellowgreen' => '#9acd32',
    ];

    /**
     * @return array{r:int,g:int,b:int,a:float}|false
     */
    public static function parseColor(string $color): array|false
    {
        $color = trim(strtolower($color));

        if (isset(self::$namedColors[$color])) {
            $color = self::$namedColors[$color];
        }

        if (preg_match('/^#([a-f0-9]{3}|[a-f0-9]{6})$/i', $color)) {
            return self::hexToRgb($color);
        }

        if (preg_match('/^rgba?\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)\s*(?:,\s*([\d.]+))?\s*\)$/i', $color, $matches)) {
            $r = (int) $matches[1];
            $g = (int) $matches[2];
            $b = (int) $matches[3];
            $a = isset($matches[4]) ? (float) $matches[4] : 1.0;

            if (! self::isRgbChannel($r) || ! self::isRgbChannel($g) || ! self::isRgbChannel($b) || ! self::isAlpha($a)) {
                return false;
            }

            return [
                'r' => $r,
                'g' => $g,
                'b' => $b,
                'a' => $a,
            ];
        }

        if (preg_match('/^hsla?\(\s*(\d+)\s*,\s*(\d+)%\s*,\s*(\d+)%\s*(?:,\s*([\d.]+))?\s*\)$/i', $color, $matches)) {
            $h = (int) $matches[1];
            $s = (int) $matches[2];
            $l = (int) $matches[3];
            $a = isset($matches[4]) ? (float) $matches[4] : 1.0;

            if ($s < 0 || $s > 100 || $l < 0 || $l > 100 || ! self::isAlpha($a)) {
                return false;
            }

            return self::hslToRgb($h, $s, $l, $a);
        }

        if (preg_match('/^oklch\(\s*([\d.]+%?)\s+([\d.]+)\s+([+-]?[\d.]+)(?:deg)?(?:\s*\/\s*([\d.]+%?))?\s*\)$/i', $color, $matches)) {
            $l = self::parseOklchLightness($matches[1]);
            $c = (float) $matches[2];
            $h = (float) $matches[3];
            $a = self::parseAlpha($matches[4] ?? null);

            if ($l === null || $c < 0 || $a === null) {
                return false;
            }

            return self::oklchToRgb($l, $c, $h, $a);
        }

        return false;
    }

    /**
     * @return array{r:int,g:int,b:int,a:float}
     */
    public static function hexToRgb(string $hex): array
    {
        $hex = ltrim($hex, '#');

        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }

        return [
            'r' => hexdec(substr($hex, 0, 2)),
            'g' => hexdec(substr($hex, 2, 2)),
            'b' => hexdec(substr($hex, 4, 2)),
            'a' => 1.0,
        ];
    }

    public static function rgbToHex(int $r, int $g, int $b): string
    {
        return sprintf('#%02x%02x%02x', $r, $g, $b);
    }

    /**
     * @return array{h:int,s:int,l:int}
     */
    public static function rgbToHsl(int $r, int $g, int $b): array
    {
        $r /= 255;
        $g /= 255;
        $b /= 255;

        $max = max($r, $g, $b);
        $min = min($r, $g, $b);
        $l = ($max + $min) / 2;
        $h = 0.0;

        if ($max === $min) {
            $s = 0.0;
        } else {
            $d = $max - $min;
            $s = $l > 0.5 ? $d / (2 - $max - $min) : $d / ($max + $min);

            switch ($max) {
                case $r:
                    $h = ($g - $b) / $d + ($g < $b ? 6 : 0);
                    break;
                case $g:
                    $h = ($b - $r) / $d + 2;
                    break;
                case $b:
                    $h = ($r - $g) / $d + 4;
                    break;
            }

            $h /= 6;
        }

        return [
            'h' => (int) round($h * 360),
            's' => (int) round($s * 100),
            'l' => (int) round($l * 100),
        ];
    }

    /**
     * @return array{l:float,c:float,h:float}
     */
    public static function rgbToOklch(int $r, int $g, int $b): array
    {
        $r /= 255;
        $g /= 255;
        $b /= 255;

        $linearize = static fn (float $val): float => $val >= 0.04045 ? (($val + 0.055) / 1.055) ** 2.4 : $val / 12.92;

        $rLinear = $linearize($r);
        $gLinear = $linearize($g);
        $bLinear = $linearize($b);

        $l = 0.4122214708 * $rLinear + 0.5363325363 * $gLinear + 0.0514459929 * $bLinear;
        $m = 0.2119034982 * $rLinear + 0.6806995451 * $gLinear + 0.1073969566 * $bLinear;
        $s = 0.0883024619 * $rLinear + 0.2817188376 * $gLinear + 0.6299787005 * $bLinear;

        $l_ = $l ** (1 / 3);
        $m_ = $m ** (1 / 3);
        $s_ = $s ** (1 / 3);

        $labL = 0.2104542553 * $l_ + 0.7936177850 * $m_ - 0.0040720468 * $s_;
        $labA = 1.9779984951 * $l_ - 2.4285922050 * $m_ + 0.4505937099 * $s_;
        $labB = 0.0259040371 * $l_ + 0.7827717662 * $m_ - 0.8086757660 * $s_;

        $c = sqrt($labA * $labA + $labB * $labB);
        $h = rad2deg(atan2($labB, $labA));

        if ($h < 0) {
            $h += 360;
        }

        return [
            'l' => round($labL, 3),
            'c' => round($c, 3),
            'h' => round($h, 1),
        ];
    }

    public static function findClosestNamedColor(int $r, int $g, int $b): string
    {
        $minDistance = PHP_INT_MAX;
        $closestColor = '';

        foreach (self::$namedColors as $name => $hex) {
            $namedRgb = self::hexToRgb($hex);
            $distance = sqrt(
                ($r - $namedRgb['r']) ** 2 +
                ($g - $namedRgb['g']) ** 2 +
                ($b - $namedRgb['b']) ** 2
            );

            if ($distance < $minDistance) {
                $minDistance = $distance;
                $closestColor = $name;
            }
        }

        return $closestColor;
    }

    /**
     * @return array{r:int,g:int,b:int,a:float}
     */
    private static function hslToRgb(int $h, int $s, int $l, float $a = 1.0): array
    {
        $h /= 360;
        $s /= 100;
        $l /= 100;

        if ($s === 0.0) {
            $r = $g = $b = $l;
        } else {
            $hue2rgb = static function (float $p, float $q, float $t): float {
                if ($t < 0) {
                    $t += 1;
                }
                if ($t > 1) {
                    $t -= 1;
                }
                if ($t < 1 / 6) {
                    return $p + ($q - $p) * 6 * $t;
                }
                if ($t < 1 / 2) {
                    return $q;
                }
                if ($t < 2 / 3) {
                    return $p + ($q - $p) * (2 / 3 - $t) * 6;
                }

                return $p;
            };

            $q = $l < 0.5 ? $l * (1 + $s) : $l + $s - $l * $s;
            $p = 2 * $l - $q;

            $r = $hue2rgb($p, $q, $h + 1 / 3);
            $g = $hue2rgb($p, $q, $h);
            $b = $hue2rgb($p, $q, $h - 1 / 3);
        }

        return [
            'r' => (int) round($r * 255),
            'g' => (int) round($g * 255),
            'b' => (int) round($b * 255),
            'a' => $a,
        ];
    }

    /**
     * @return array{r:int,g:int,b:int,a:float}
     */
    private static function oklchToRgb(float $l, float $c, float $h, float $a = 1.0): array
    {
        $hRad = deg2rad($h);
        $labA = $c * cos($hRad);
        $labB = $c * sin($hRad);

        $l_ = $l + 0.3963377774 * $labA + 0.2158037573 * $labB;
        $m_ = $l - 0.1055613458 * $labA - 0.0638541728 * $labB;
        $s_ = $l - 0.0894841775 * $labA - 1.2914855480 * $labB;

        $l = $l_ ** 3;
        $m = $m_ ** 3;
        $s = $s_ ** 3;

        $rLinear = +4.0767416621 * $l - 3.3077115913 * $m + 0.2309699292 * $s;
        $gLinear = -1.2684380046 * $l + 2.6097574011 * $m - 0.3413193965 * $s;
        $bLinear = -0.0041960863 * $l - 0.7034186147 * $m + 1.7076147010 * $s;

        $gammaCorrect = static fn (float $val): float => $val >= 0.0031308 ? 1.055 * $val ** (1 / 2.4) - 0.055 : 12.92 * $val;

        $r = max(0, min(1, $gammaCorrect($rLinear))) * 255;
        $g = max(0, min(1, $gammaCorrect($gLinear))) * 255;
        $b = max(0, min(1, $gammaCorrect($bLinear))) * 255;

        return [
            'r' => (int) round($r),
            'g' => (int) round($g),
            'b' => (int) round($b),
            'a' => $a,
        ];
    }

    private static function isRgbChannel(int $value): bool
    {
        return $value >= 0 && $value <= 255;
    }

    private static function isAlpha(float $value): bool
    {
        return $value >= 0 && $value <= 1;
    }

    private static function parseAlpha(?string $value): ?float
    {
        if ($value === null || $value === '') {
            return 1.0;
        }

        $value = trim($value);

        if (str_ends_with($value, '%')) {
            $percent = (float) rtrim($value, '%');

            if ($percent < 0 || $percent > 100) {
                return null;
            }

            return $percent / 100;
        }

        $alpha = (float) $value;

        return self::isAlpha($alpha) ? $alpha : null;
    }

    private static function parseOklchLightness(string $value): ?float
    {
        $value = trim($value);

        if (str_ends_with($value, '%')) {
            $percent = (float) rtrim($value, '%');

            if ($percent < 0 || $percent > 100) {
                return null;
            }

            return $percent / 100;
        }

        $number = (float) $value;

        if ($number < 0 || $number > 1) {
            return null;
        }

        return $number;
    }
}
