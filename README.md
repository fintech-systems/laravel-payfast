# Laravel PayFast
![GitHub release (latest by date)](https://img.shields.io/github/v/release/fintech-systems/packagist-boilerplate) [![Build Status](https://app.travis-ci.com/fintech-systems/packagist-boilerplate.svg?branch=main)](https://app.travis-ci.com/fintech-systems/packagist-boilerplate) ![GitHub](https://img.shields.io/github/license/fintech-systems/packagist-boilerplate)

A PayFast API designed to run standalone or part of a Laravel Application

Requirements:

- PHP 8.0
- Laravel
- A PayFast account

## Installation

Install the package via composer:

```bash
composer require fintech-systems/laravel-payfast
```

## Publish Laravel configuration and views

Publish the config file with:
```bash
php artisan vendor:publish --provider="FintechSystems\Payfast\PayfastServiceProvider" --tag="payfast-config"
```

Publish default Success, Cancelled, and Notify (ITN) views with:
```bash
php artisan vendor:publish --provider="FintechSystems\Payfast\PayfastServiceProvider" --tag="payfast-views"
```

If you're using subscriptions, run the migrations:
```bash
php artisan migrate
```

## Usage

### Examples

- Generate a payment link
- Create an ad-hoc token optionally specifying the amount
- Cancel a subscription
- Update a card

```php
use FintechSystems\Payfast\Facades\Payfast;

Route::get('/payment', function() {
    return Payfast::payment(5,'Order #1');
});

Route::get('/create-token', function() {
    return Payfast::createToken(5);
});

Route::get('/cancel-subscription', function() {
    return Payfast::cancelSubscription('0581fff3-2f69-4b89-827e-6057c76893cc');
});

Route::get('/update-card', function() {
    return Payfast::updateCardLink('40ab3194-20f0-4814-8c89-4d2a6b5462ed');
});
```

## Testing

```bash
vendor/bin/phpunit
```

### Local Editing

For local editing, add this to `composer.json`:

```json
"repositories" : [
        {
            "type": "path",
            "url": "../laravel-payfast"
        }
    ]
```

Then in `require` section:

```json
"fintech-systems/laravel-payfast": "dev-main",
```

```bash
composer require fintech-systems/laravel-payfast
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Credits

- [Eugene van der Merwe](https://github.com/eugenevdm)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
