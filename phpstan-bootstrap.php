<?php

declare(strict_types=1);

namespace WP_CLI\Utils {
    function get_flag_value(array $assoc_args, string $flag, mixed $default = null): mixed
    {
        return $assoc_args[$flag] ?? $default;
    }

    function format_items(string $format, array $items, array $fields): void
    {
    }
}

namespace {
    if (! function_exists('esc_html')) {
        function esc_html(string $text): string
        {
            return $text;
        }
    }

    if (! function_exists('sanitize_title')) {
        function sanitize_title(string $title): string
        {
            return $title;
        }
    }

    if (! function_exists('wp_json_encode')) {
        function wp_json_encode(mixed $value, int $flags = 0, int $depth = 512): string|false
        {
            return json_encode($value, $flags, $depth);
        }
    }

    if (! class_exists('WP_CLI')) {
        class WP_CLI
        {
            public static function log(string $message): void {}

            public static function warning(string $message): void {}

            public static function success(string $message): void {}

            public static function add_command(string $name, mixed $callable): void {}

            public static function has_command(string $name): bool
            {
                return false;
            }

            public static function add_hook(string $when, callable $callback): void {}

            public static function get_runner(): ?object
            {
                return null;
            }

            public static function error(string $message): never
            {
                throw new \RuntimeException($message);
            }
        }
    }
}
