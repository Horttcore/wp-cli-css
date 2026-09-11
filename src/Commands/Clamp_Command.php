<?php

declare(strict_types=1);

namespace RalfHortt\WpCliCss\Commands;

use RalfHortt\WpCliCss\Support\ClampMath;
use RalfHortt\WpCliCss\Support\TokenResolver;
use RalfHortt\WpCliShared\Support\PromptHelper;

final class Clamp_Command
{
    /**
     * Calculate a CSS clamp() value for fluid typography or spacing.
     *
     * ## OPTIONS
     *
     * [--theme=<theme>]
     * : Theme stylesheet slug override.
     *
     * [--min-viewport=<min-viewport>]
     * : Minimum viewport width in pixels.
     *
     * [--max-viewport=<max-viewport>]
     * : Maximum viewport width in pixels.
     *
     * [--min=<min>]
     * : Minimum value (number, px, rem, var:preset..., or CSS var).
     *
     * [--max=<max>]
     * : Maximum value (number, px, rem, var:preset..., or CSS var).
     *
     * [--unit=<unit>]
     * : Output unit for min and max values.
     * ---
     * default: rem
     * options:
     *   - rem
     *   - px
     * ---
     *
     * [--base-size=<base-size>]
     * : Base font size in pixels used for px->rem conversion.
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
     */
    public function __invoke(array $args, array $assoc_args): void
    {
        $themeSlug = \WP_CLI\Utils\get_flag_value($assoc_args, 'theme');
        $tokenResolver = new TokenResolver(is_string($themeSlug) ? $themeSlug : null);
        $sizeSuggestions = $tokenResolver->getSizeSuggestions();
        $viewportOptions = $tokenResolver->getViewportWidthsLabeled();
        $viewportDefaults = $tokenResolver->getViewportDefaults();

        if ($viewportOptions === []) {
            \WP_CLI::warning('No viewport widths found in theme.json settings.viewport. Using defaults.');
        }

        if ($sizeSuggestions === []) {
            \WP_CLI::warning('No theme size tokens found. Pass --path for theme.json integration.');
        }

        $minViewportInput = PromptHelper::suggestOrFlag(
            $assoc_args,
            'min-viewport',
            'Enter minimum viewport width (px)',
            $viewportOptions,
            $viewportDefaults['min'],
            allowFreeText: true,
        );

        $maxViewportInput = PromptHelper::suggestOrFlag(
            $assoc_args,
            'max-viewport',
            'Enter maximum viewport width (px)',
            $viewportOptions,
            $viewportDefaults['max'],
            allowFreeText: true,
        );

        $minViewport = $tokenResolver->parseViewportInput($minViewportInput);
        $maxViewport = $tokenResolver->parseViewportInput($maxViewportInput);

        if ($maxViewport <= $minViewport) {
            \WP_CLI::error('Maximum viewport must be greater than minimum viewport.');
        }

        $unit = (string) \WP_CLI\Utils\get_flag_value($assoc_args, 'unit', 'rem');

        if (! in_array($unit, ['rem', 'px'], true)) {
            \WP_CLI::error('Invalid --unit. Use rem or px.');
        }

        $baseSizeInput = (string) \WP_CLI\Utils\get_flag_value($assoc_args, 'base-size', '16');
        $baseSize = (float) $baseSizeInput;

        if ($baseSize <= 0) {
            \WP_CLI::error('Invalid --base-size. Use a value greater than 0.');
        }

        $minInput = PromptHelper::suggestOrFlag(
            $assoc_args,
            'min',
            'Enter minimum value',
            $sizeSuggestions,
            allowFreeText: true,
        );

        $maxInput = PromptHelper::suggestOrFlag(
            $assoc_args,
            'max',
            'Enter maximum value',
            $sizeSuggestions,
            allowFreeText: true,
        );

        $minValue = $this->parseSizeInput($tokenResolver->resolveColorInput($minInput), $baseSize);
        $maxValue = $this->parseSizeInput($tokenResolver->resolveColorInput($maxInput), $baseSize);

        if ($minValue === null || $maxValue === null) {
            \WP_CLI::error('Invalid min/max value. Use a number, px, rem, or a resolvable theme token.');
        }

        if ($maxValue <= $minValue) {
            \WP_CLI::error('Maximum value must be greater than minimum value.');
        }

        $format = (string) \WP_CLI\Utils\get_flag_value($assoc_args, 'format', 'line');

        $clamp = ClampMath::calculateClamp($minViewport, $maxViewport, $minValue, $maxValue, $unit, $baseSize);

        $row = [
            'min_viewport' => $minViewport,
            'max_viewport' => $maxViewport,
            'min' => $minValue,
            'max' => $maxValue,
            'unit' => $unit,
            'base_size' => $baseSize,
            'clamp' => $clamp,
        ];

        if ($format !== 'line') {
            \WP_CLI\Utils\format_items($format, [$row], array_keys($row));

            return;
        }

        \WP_CLI::log($clamp);
    }

    private function parseSizeInput(string $input, float $baseSize): ?float
    {
        $input = trim($input);

        if ($input === '') {
            return null;
        }

        if (preg_match('/^(-?\d+(?:\.\d+)?)(px|rem)?$/i', $input, $matches)) {
            $value = (float) $matches[1];
            $unit = strtolower((string) ($matches[2] ?? 'px'));

            if ($unit === 'rem') {
                return $value * $baseSize;
            }

            return $value;
        }

        return null;
    }
}
