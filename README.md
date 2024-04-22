# PHP Enum to JS for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/hackel/php-enum-to-js.svg?style=flat-square)](https://packagist.org/packages/hackel/php-enum-to-js)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/hackel/php-enum-to-js/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/hackel/php-enum-to-js/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/hackel/php-enum-to-js/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/hackel/php-enum-to-js/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/hackel/php-enum-to-js.svg?style=flat-square)](https://packagist.org/packages/hackel/php-enum-to-js)

This package will scan and automatically convert your PHP enums into JavaScript objects.

### Example:
Input:
```php
namespace App\Enums;

enum Color: string
{
    case RED = 'red';
    case GREEN = 'green';
    case BLUE = 'blue';
}
```
Output:
```javascript
export default {
    "RED": "red",
    "GREEN": "green",
    "BLUE": "blue"
};
```

## Installation

You can install the package via composer:

```bash
composer require hackel/php-enum-to-js
```

Optionally, you can publish the stub file using:

```bash
php artisan vendor:publish --tag="php-enum-to-js-stub"
```

This will create an `enum.stub` file in your `stubs` directory. You can then customize this file to decorate your JavaScript enum however you like.

## Usage

```bash
php artisan enum:to-js:convert
```
### Options
- `--source` - The directory to scan for PHP enums. Defaults to `app/Enums`.
- `--dest` - The directory to write the JavaScript enums to. Defaults to `resources/js/enums`.
- `--clean` - If set, all files in the destination directory will be removed before writing the JavaScript enums.
- `--no-dump-autoload` Do not try to run `composer dump-autoload` prior to converting.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Ryan Hayle](https://github.com/hackel)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
