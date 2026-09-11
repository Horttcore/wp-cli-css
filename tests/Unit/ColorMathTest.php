<?php

declare(strict_types=1);

use RalfHortt\WpCliCss\Support\ColorMath;

it('parses rgb channels within bounds', function (): void {
    $parsed = ColorMath::parseColor('rgb(12, 34, 56)');

    expect($parsed)->toBeArray()
        ->and($parsed['r'])->toBe(12)
        ->and($parsed['g'])->toBe(34)
        ->and($parsed['b'])->toBe(56)
        ->and($parsed['a'])->toBe(1.0);
});

it('rejects rgb channels out of bounds', function (): void {
    expect(ColorMath::parseColor('rgb(300, 10, 10)'))->toBeFalse();
});

it('rejects invalid alpha in rgba', function (): void {
    expect(ColorMath::parseColor('rgba(10, 20, 30, 1.2)'))->toBeFalse();
});

it('parses oklch values with percent lightness and alpha percent', function (): void {
    $parsed = ColorMath::parseColor('oklch(62% 0.24 265deg / 80%)');

    expect($parsed)->toBeArray()
        ->and($parsed['r'])->toBeInt()
        ->and($parsed['g'])->toBeInt()
        ->and($parsed['b'])->toBeInt()
        ->and($parsed['a'])->toBe(0.8);
});

it('rejects oklch lightness above 100 percent', function (): void {
    expect(ColorMath::parseColor('oklch(120% 0.2 220)'))->toBeFalse();
});

it('finds closest named color for exact red', function (): void {
    expect(ColorMath::findClosestNamedColor(255, 0, 0))->toBe('red');
});
