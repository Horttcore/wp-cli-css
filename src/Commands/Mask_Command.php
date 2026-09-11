<?php

declare(strict_types=1);

namespace RalfHortt\WpCliCss\Commands;

use RalfHortt\WpCliShared\Support\FileGuard;
use RalfHortt\WpCliShared\Support\PromptHelper;

final class Mask_Command
{
    /**
     * Convert SVG markup to CSS mask properties.
     *
     * ## OPTIONS
     *
     * [--svg=<svg>]
     * : SVG markup.
     *
     * [--output=<output>]
     * : Write CSS output to a file.
     *
     * [--force]
     * : Overwrite existing output file.
     *
     * [--no-interaction]
     * : Disable interactive prompts.
     *
     * @param  array<int, string>  $args
     * @param  array<string, mixed>  $assoc_args
     */
    public function __invoke(array $args, array $assoc_args): void
    {
        $svgInput = \WP_CLI\Utils\get_flag_value($assoc_args, 'svg');

        if ($svgInput === null || $svgInput === '') {
            if (! PromptHelper::isInteractive($assoc_args)) {
                \WP_CLI::error('Missing required --svg (non-interactive mode).');
            }

            $svgInput = PromptHelper::textarea(
                'Paste your SVG code',
                'Example: <svg xmlns="http://www.w3.org/2000/svg">...</svg>'
            );
        }

        if (! $this->validateSvg((string) $svgInput)) {
            \WP_CLI::error('Invalid SVG format.');
        }

        $dataUri = $this->svgToDataUri((string) $svgInput);
        $css = $this->generateCssOutput($dataUri);

        $output = \WP_CLI\Utils\get_flag_value($assoc_args, 'output');

        if ($output) {
            FileGuard::assertCanWrite((string) $output, $assoc_args);

            if (file_put_contents((string) $output, $css) === false) {
                \WP_CLI::error('Failed to write CSS output file.');
            }

            \WP_CLI::success(sprintf('CSS mask written to %s', $output));

            return;
        }

        \WP_CLI::log($css);
    }

    private function validateSvg(string $svg): bool
    {
        $svg = trim($svg);

        if ($svg === '' || ! preg_match('/<svg/i', $svg)) {
            return false;
        }

        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        $success = $dom->loadXML($svg);
        libxml_use_internal_errors(false);

        return $success !== false;
    }

    private function svgToDataUri(string $svg): string
    {
        $svg = trim($svg);

        if (! preg_match('/xmlns\s*=/', $svg)) {
            $svg = preg_replace('/<svg/i', '<svg xmlns="http://www.w3.org/2000/svg"', $svg, 1) ?? $svg;
        }

        $svg = str_replace(["\r", "\n"], '', $svg);
        $svg = trim($svg);
        $svg = str_replace('"', "'", $svg);
        $svg = preg_replace('/>\s+</', '><', $svg) ?? $svg;

        $encoded = rawurlencode($svg);
        $encoded = str_replace('%2F', '/', $encoded);

        return 'data:image/svg+xml,' . $encoded;
    }

    private function generateCssOutput(string $dataUri): string
    {
        return <<<CSS
mask-image: url("{$dataUri}");
mask-size: contain;
mask-repeat: no-repeat;
mask-position: center;
CSS;
    }
}
