# Laravel PayFast
![GitHub release (latest by date)](https://img.shields.io/github/v/release/fintech-systems/packagist-boilerplate) [![Build Status](https://app.travis-ci.com/fintech-systems/packagist-boilerplate.svg?branch=main)](https://app.travis-ci.com/fintech-systems/packagist-boilerplate) ![GitHub](https://img.shields.io/github/license/fintech-systems/packagist-boilerplate)

A PayFast API designed to run standalone or part of a Laravel Application

Requirements:

- PHP 8.0
- Laravel
- A PayFast account

## Installation

You can install the package via composer:

```bash
composer require fintech-systems/laravel-payfast
```

## Publish Laravel configuration

You can publish the config file with:
```bash
php artisan vendor:publish --provider="FintechSystems\Payfast\PayfastServiceProvider" --tag="payfast-config"
```

## Usage

```php
use FintechSystems\Payfast\Facades\Payfast;

Route::get('/payment', function() {
    return Payfast::payment(5,'Order #1');
});
```

## Testing

```bash
vendor/bin/phpunit
```

Use the command below to run tests that excludes touching the API:

`vendor/bin/phpunit --exclude-group=live`

The `storage` folder has examples API responses, also used for caching during tests.

### Coverage reports

To regenerate coverage reports:

`XDEBUG_MODE=coverage ./vendor/bin/phpunit --coverage-html=tests/coverage-report`

See also `.travis.yml`

### Local Editing

For local editing, add this to `composer.json`:

```json
"repositories" : [
        {
            "type": "path",
            "url": "../technology-api"
        }
    ]
```

Then in `require` section:

```json
"fintech-systems/technology-api": "dev-main",
```

## Version Control

This application uses Semantic Versioning as per https://semver.org/

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Credits

- [Eugene van der Merwe](https://github.com/fintech-systems)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
