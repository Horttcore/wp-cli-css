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

WP_CLI::add_command('css color', Color_Command::class);
WP_CLI::add_command('css contrast', Contrast_Command::class);
WP_CLI::add_command('css clamp', Clamp_Command::class);
WP_CLI::add_command('css mask', Mask_Command::class);
