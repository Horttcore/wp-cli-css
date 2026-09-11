<?php

declare(strict_types=1);

namespace RalfHortt\WpCliCss\Commands;

use RalfHortt\WpCliCss\Support\ColorMath;
use RalfHortt\WpCliCss\Support\ContrastMath;
use RalfHortt\WpCliCss\Support\TokenResolver;
use RalfHortt\WpCliShared\Support\PromptHelper;

final class Contrast_Command
{
    /**
     * Calculate WCAG contrast ratio between two colors.
     *
     * ## OPTIONS
     *
     * [<foreground>]
     * : Foreground color or theme token.
     *
     * [<background>]
     * : Background color or theme token.
     *
     * [--theme=<theme>]
     * : Theme stylesheet slug override.
     *
     * [--no-interaction]
     * : Disable interactive prompts.
     */
    public function __invoke(array $args, array $assoc_args): void
    {
        $themeSlug = \WP_CLI\Utils\get_flag_value($assoc_args, 'theme');
        $tokenResolver = new TokenResolver(is_string($themeSlug) ? $themeSlug : null);
        $suggestions = $tokenResolver->getColorSuggestions();

        if ($suggestions === []) {
            \WP_CLI::warning('No theme color tokens found. Pass --path for theme.json integration.');
        }

        if (isset($args[0])) {
            $foreground = (string) $args[0];
        } else {
            $foreground = PromptHelper::suggestOrFlag(
                $assoc_args,
                'foreground',
                'Enter the foreground color',
                $suggestions,
                allowFreeText: true,
            );
        }

        if (isset($args[1])) {
            $background = (string) $args[1];
        } else {
            $background = PromptHelper::suggestOrFlag(
                $assoc_args,
                'background',
                'Enter the background color',
                $suggestions,
                '#ffffff',
                allowFreeText: true,
            );
        }

        $rgb1 = ColorMath::parseColor($tokenResolver->resolveColorInput($foreground));
        $rgb2 = ColorMath::parseColor($tokenResolver->resolveColorInput($background));

        if ($rgb1 === false) {
            \WP_CLI::error(sprintf('Invalid foreground color: %s', $foreground));
        }

        if ($rgb2 === false) {
            \WP_CLI::error(sprintf('Invalid background color: %s', $background));
        }

        $contrastRatio = ContrastMath::calculateContrastRatio($rgb1, $rgb2);
        $status = ContrastMath::getAccessibilityStatus($contrastRatio);

        $ratio = number_format($contrastRatio, 2) . ':1';
        $aaNormal = $status['AA_normal'] ? 'pass' : 'fail';
        $aaLarge = $status['AA_large'] ? 'pass' : 'fail';
        $aaaNormal = $status['AAA_normal'] ? 'pass' : 'fail';
        $aaaLarge = $status['AAA_large'] ? 'pass' : 'fail';

        \WP_CLI::log("$foreground vs $background → Ratio: $ratio | AA: $aaNormal Normal, $aaLarge Large | AAA: $aaaNormal Normal, $aaaLarge Large");
    }
}
