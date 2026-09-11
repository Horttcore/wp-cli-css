# ralfhortt/wp-cli-css

[![CI](https://github.com/Horttcore/wp-cli-css/actions/workflows/ci.yml/badge.svg)](https://github.com/Horttcore/wp-cli-css/actions/workflows/ci.yml)

WP-CLI CSS utilities with theme.json token autocomplete.

## Installation

Published package:

```bash
wp package install ralfhortt/wp-cli-css
```

`ralfhortt/wp-cli-shared` is installed automatically as a Composer dependency.

Local development checkout:

```bash
wp package install /absolute/path/to/wp-cli-css
```

Update after local edits:

```bash
wp package remove ralfhortt/wp-cli-css
wp package install /absolute/path/to/wp-cli-css
```

## Commands

| Command | Description |
|---|---|
| `wp css color [<color>]` | Convert colors between HEX, RGB, HSL, OKLCH |
| `wp css contrast [<fg>] [<bg>]` | WCAG contrast ratio |
| `wp css clamp` | Fluid `clamp()` calculator with unit controls |
| `wp css mask` | SVG to CSS mask properties |

Pass `--path=/path/to/wordpress` for theme.json token suggestions.

## Examples

```bash
wp css color primary --path=/path/to/wordpress
wp css contrast "#000" "#fff" --path=/path/to/wordpress
wp css clamp --min-viewport=320 --max-viewport=1920 --min=16 --max=24 --path=/path/to/wordpress
wp css color "oklch(62% 0.24 265deg / 80%)" --format=json --path=/path/to/wordpress
wp css contrast var(--wp--preset--color--contrast) var(--wp--preset--color--base) --format=table --path=/path/to/wordpress
wp css clamp --min=1rem --max=2rem --unit=rem --base-size=16 --format=json --path=/path/to/wordpress
```

## Output formats

- `wp css color` supports `--format=line|json|table|csv|yaml`
- `wp css contrast` supports `--format=line|json|table|csv|yaml`
- `wp css clamp` supports `--format=line|json|table|csv|yaml`

## Clamp options

- `--unit=rem|px` controls output min/max units
- `--base-size=<px>` sets conversion base size for `rem`

## Development

```bash
composer stan
composer lint
composer test
```
