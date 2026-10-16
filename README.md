# MenuBuilder Helper

[![GitHub License](https://img.shields.io/github/license/pieceofcake2/menu_builder?label=License)](LICENSE)
[![Packagist Version](https://img.shields.io/packagist/v/pieceofcake2/menu_builder?label=Packagist)](https://packagist.org/packages/pieceofcake2/menu_builder)
![PHP](https://img.shields.io/packagist/dependency-v/pieceofcake2/menu_builder/php?logo=php&logoColor=%23FFFFFF&label=PHP&labelColor=%23777BB4&color=%23FFFFFF)
![CakePHP](https://img.shields.io/packagist/dependency-v/pieceofcake2/menu_builder/pieceofcake2/cakephp?logo=cakephp&logoColor=%23FFFFFF&label=CakePHP&labelColor=%23D33C43&color=%23FFFFFF)
[![PHPStan](https://img.shields.io/badge/PHPStan-level%205-brightgreen?label=PHPStan)](https://github.com/pieceofcake2//Users/tsuyoshi/dev/pieceofcake2/menu_builder/actions/workflows/CI.yml)
[![CI](https://img.shields.io/github/actions/workflow/status/pieceofcake2/menu_builder/CI.yml?label=CI)](https://github.com/pieceofcake2/menu_builder/actions/workflows/CI.yml)
[![Codecov](https://img.shields.io/codecov/c/gh/pieceofcake2/menu_builder?label=Coverage)](https://codecov.io/gh/pieceofcake2/menu_builder)

A dynamic menu building helper for CakePHP 2 with multi-level menus, CSS classes for the active,
first and parent items, and menus based on the current user's group. This is a fork of
[torifat/cake-menu_builder](https://github.com/torifat/cake-menu_builder) for
[pieceofcake2/cakephp](https://github.com/pieceofcake2/cakephp).

## Documentation

- Website: https://pieceofcake2.github.io/menu_builder/ (日本語: https://pieceofcake2.github.io/menu_builder/ja/)
- Markdown: [docs/README.md](docs/README.md) (日本語: [docs/ja/README.md](docs/ja/README.md)) (for AI tools and offline reading)

The Markdown documentation is also included in the Composer package, in the plugin's directory: `plugins/MenuBuilder/docs/README.md` (the location depends on your `installer-paths`). Install the package with `--dev` if you only need it for development.

## Requirements

* PHP 8.0+
* pieceofcake2/cakephp 2.12+

## Installation

```bash
composer require pieceofcake2/menu_builder
```

## Contributing

See the [contribution guidelines](https://github.com/pieceofcake2/.github/blob/master/CONTRIBUTING.md).

## Origin

This package is a maintained fork of
[torifat/cake-menu_builder](https://github.com/torifat/cake-menu_builder)
(`torifat/cake-menu_builder` on Packagist, last upstream version v2.0.3), originally by Rifat Nabi.
It is maintained for CakePHP 2 on modern PHP by [pieceofcake2](https://github.com/pieceofcake2).
Changes from the original are listed in the
[commit history since the fork](https://github.com/pieceofcake2/menu_builder/compare/b515602...2.x).

## License

The MIT License. See [LICENSE](LICENSE).
