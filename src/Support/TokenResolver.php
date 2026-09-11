<?php

declare(strict_types=1);

namespace RalfHortt\WpCliCss\Support;

use RalfHortt\WpCliShared\ThemeToken;
use RalfHortt\WpCliShared\WordPressData;

final class TokenResolver
{
    private WordPressData $wpData;

    public function __construct(?string $themeSlug = null)
    {
        $this->wpData = new WordPressData($themeSlug);
    }

    /**
     * @return array<int, string>
     */
    public function getColorSuggestions(): array
    {
        $suggestions = [];

        foreach ($this->wpData->getTokensByType('color') as $token) {
            $suggestions = array_merge($suggestions, $token->suggestionForms());
        }

        return array_values(array_unique($suggestions));
    }

    /**
     * @return array<int, string>
     */
    public function getSizeSuggestions(): array
    {
        $suggestions = [];

        foreach ($this->wpData->getTokensByType('size') as $token) {
            $suggestions = array_merge($suggestions, $token->suggestionForms());
        }

        return array_values(array_unique($suggestions));
    }

    /**
     * @return array<string, string>
     */
    public function getViewportWidthsLabeled(): array
    {
        return $this->wpData->getViewportWidthsLabeled();
    }

    /**
     * @return array{min: string, max: string}
     */
    public function getViewportDefaults(): array
    {
        $widths = $this->wpData->getViewportWidths();

        if ($widths === []) {
            return ['min' => '320', 'max' => '1920'];
        }

        $pixels = [];

        foreach ($widths as $slug => $value) {
            $pixelValue = $this->wpData->parsePixelValue($value);

            if ($pixelValue !== null) {
                $pixels[$slug] = $pixelValue;
            }
        }

        if ($pixels === []) {
            return ['min' => '320', 'max' => '1920'];
        }

        $minSlug = array_key_exists('mobile', $pixels)
            ? 'mobile'
            : array_key_first($pixels);

        $maxSlug = array_key_exists('desktop', $pixels)
            ? 'desktop'
            : (array_key_exists('tablet', $pixels) ? 'tablet' : array_key_last($pixels));

        return [
            'min' => (string) (int) $pixels[$minSlug],
            'max' => (string) (int) $pixels[$maxSlug],
        ];
    }

    public function parseViewportInput(string $input): float
    {
        $widths = $this->wpData->getViewportWidths();

        if (isset($widths[$input])) {
            return $this->wpData->parsePixelValue($widths[$input]) ?? (float) $input;
        }

        $parsed = $this->wpData->parsePixelValue($input);

        return $parsed ?? (float) $input;
    }

    public function resolveColorInput(string $input): string
    {
        if (ColorMath::parseColor($input) !== false) {
            return $input;
        }

        $resolved = $this->resolveToken($input);

        if ($resolved !== null) {
            return $resolved;
        }

        return $input;
    }

    private function resolveToken(string $input): ?string
    {
        $input = trim($input);

        foreach ($this->wpData->getAllTokens() as $token) {
            foreach ($token->suggestionForms() as $form) {
                if ($form === $input) {
                    return $token->resolved;
                }
            }
        }

        return null;
    }
}
