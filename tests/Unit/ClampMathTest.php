<?php

declare(strict_types=1);

use RalfHortt\WpCliCss\Support\ClampMath;

it('parses pixel value with px suffix', function (): void {
    expect(ClampMath::parsePixelValue('24px'))->toBe(24.0);
});

it('parses pixel value without unit', function (): void {
    expect(ClampMath::parsePixelValue('12.5'))->toBe(12.5);
});

it('returns null for unsupported pixel value unit', function (): void {
    expect(ClampMath::parsePixelValue('2rem'))->toBeNull();
});

it('converts px to rem', function (): void {
    expect(ClampMath::pxToRem(24.0, 16.0))->toBe(1.5);
});

it('calculates rem clamp string', function (): void {
    $clamp = ClampMath::calculateClamp(320, 1280, 16, 32, 'rem', 16.0);

    expect($clamp)->toContain('clamp(')
        ->and($clamp)->toContain('1rem')
        ->and($clamp)->toContain('2rem')
        ->and($clamp)->toContain('vw');
});

it('calculates px clamp string', function (): void {
    $clamp = ClampMath::calculateClamp(320, 1280, 16, 32, 'px', 16.0);

    expect($clamp)->toContain('16px')
        ->and($clamp)->toContain('32px')
        ->and($clamp)->toContain('vw');
});
