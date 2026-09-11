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
     * : Minimum value in pixels.
     *
     * [--max=<max>]
     * : Maximum value in pixels.
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

        $minValue = (float) PromptHelper::textOrFlag(
            $assoc_args,
            'min',
            'Enter minimum value (px)',
            '',
        );

        $maxValue = (float) PromptHelper::textOrFlag(
            $assoc_args,
            'max',
            'Enter maximum value (px)',
            '',
        );

        if ($maxViewport <= $minViewport) {
            \WP_CLI::error('Maximum viewport must be greater than minimum viewport.');
        }

        $clamp = ClampMath::calculateClamp($minViewport, $maxViewport, $minValue, $maxValue, 'rem');

        \WP_CLI::log($clamp);
    }
}
