# ralfhortt/wp-cli-css

WP-CLI CSS utilities with theme.json token autocomplete.

## Installation

```bash
wp package install /path/to/packages/wp-cli-shared
wp package install /path/to/packages/wp-cli-css
```

## Commands

| Command | Description |
|---|---|
| `wp css color [<color>]` | Convert colors between HEX, RGB, HSL, OKLCH |
| `wp css contrast [<fg>] [<bg>]` | WCAG contrast ratio |
| `wp css clamp` | Fluid `clamp()` calculator |
| `wp css mask` | SVG to CSS mask properties |

Pass `--path=/path/to/wordpress` for theme.json token suggestions.

## Examples

```bash
wp css color primary --path=/path/to/wordpress
wp css contrast "#000" "#fff" --path=/path/to/wordpress
wp css clamp --min-viewport=320 --max-viewport=1920 --min=16 --max=24 --path=/path/to/wordpress
```
