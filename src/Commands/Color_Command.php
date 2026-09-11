<?php

declare(strict_types=1);

namespace RalfHortt\WpCliCss\Commands;

use RalfHortt\WpCliCss\Support\ColorMath;
use RalfHortt\WpCliCss\Support\TokenResolver;
use RalfHortt\WpCliShared\Support\PromptHelper;

final class Color_Command
{
    /**
     * Convert CSS colors between formats.
     *
     * ## OPTIONS
     *
     * [<color>]
     * : CSS color value or theme token.
     *
     * [--theme=<theme>]
     * : Theme stylesheet slug override.
     *
     * [--format=<format>]
     * : Output format.
     * ---
     * default: line
     * options:
     *   - line
     *   - json
     *   - table
     *   - csv
     *   - yaml
     * ---
     *
     * [--no-interaction]
     * : Disable interactive prompts.
     *
     * @param  array<int, string>  $args
     * @param  array<string, mixed>  $assoc_args
     */
    public function __invoke(array $args, array $assoc_args): void
    {
        $themeSlug = \WP_CLI\Utils\get_flag_value($assoc_args, 'theme');
        $tokenResolver = new TokenResolver(is_string($themeSlug) ? $themeSlug : null);

        if (isset($args[0])) {
            $colorInput = (string) $args[0];
        } else {
            $suggestions = $tokenResolver->getColorSuggestions();

            if ($suggestions === []) {
                \WP_CLI::warning('No theme color tokens found. Pass --path for theme.json integration.');
            }

            $colorInput = PromptHelper::suggestOrFlag(
                $assoc_args,
                'color',
                'Enter a CSS color',
                $suggestions,
                allowFreeText: true,
            );
        }

        $resolved = $tokenResolver->resolveColorInput($colorInput);
        $rgb = ColorMath::parseColor($resolved);

        if ($rgb === false) {
            \WP_CLI::error(sprintf('Invalid color format: %s', $colorInput));
        }

        $hex = ColorMath::rgbToHex($rgb['r'], $rgb['g'], $rgb['b']);
        $hsl = ColorMath::rgbToHsl($rgb['r'], $rgb['g'], $rgb['b']);
        $oklch = ColorMath::rgbToOklch($rgb['r'], $rgb['g'], $rgb['b']);
        $closestNamed = ColorMath::findClosestNamedColor($rgb['r'], $rgb['g'], $rgb['b']);
        $format = (string) \WP_CLI\Utils\get_flag_value($assoc_args, 'format', 'line');

        $row = [
            'input' => $colorInput,
            'resolved' => $resolved,
            'hex' => $hex,
            'rgb' => sprintf('rgb(%d, %d, %d)', $rgb['r'], $rgb['g'], $rgb['b']),
            'hsl' => sprintf('hsl(%d, %d%%, %d%%)', $hsl['h'], $hsl['s'], $hsl['l']),
            'oklch' => sprintf('oklch(%s %s %s)', $oklch['l'], $oklch['c'], $oklch['h']),
            'named' => $closestNamed,
        ];

        if ($format !== 'line') {
            \WP_CLI\Utils\format_items($format, [$row], array_keys($row));

            return;
        }

        $formats = [
            "HEX: $hex",
            "RGB: rgb({$rgb['r']}, {$rgb['g']}, {$rgb['b']})",
            "HSL: hsl({$hsl['h']}, {$hsl['s']}%, {$hsl['l']}%)",
            "OKLCH: oklch({$oklch['l']} {$oklch['c']} {$oklch['h']})",
            "Named: $closestNamed",
        ];

        \WP_CLI::log("$colorInput → " . implode(' | ', $formats));
    }
}
