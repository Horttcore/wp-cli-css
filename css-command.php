<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use RalfHortt\WpCliCss\Commands\Clamp_Command;
use RalfHortt\WpCliCss\Commands\Color_Command;
use RalfHortt\WpCliCss\Commands\Contrast_Command;
use RalfHortt\WpCliCss\Commands\Mask_Command;
use RalfHortt\WpCliShared\Bootstrap;

if (! class_exists('WP_CLI')) {
    return;
}

Bootstrap::registerPromptFallback();

$registerIfMissing = static function (string $name, string $class): void {
    if (method_exists('WP_CLI', 'has_command') && WP_CLI::has_command($name)) {
        return;
    }

    WP_CLI::add_command($name, $class);
};

$registerIfMissing('css color', Color_Command::class);
$registerIfMissing('css contrast', Contrast_Command::class);
$registerIfMissing('css clamp', Clamp_Command::class);
$registerIfMissing('css mask', Mask_Command::class);
