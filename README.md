## About Laravel PayFast
![GitHub release (latest by date)](https://img.shields.io/github/v/release/fintech-systems/laravel-payfast) [![Build Status](https://app.travis-ci.com/fintech-systems/laravel-payfast.svg?branch=main)](https://app.travis-ci.com/fintech-systems/laravel-payfast) ![GitHub](https://img.shields.io/github/license/fintech-systems/laravel-payfast)

A PayFast API designed to run standalone or part of a Laravel Application

** THIS IS PRE-RELEASE SOFTWARE **

Requirements:

- PHP 8.0
- Laravel
- A Payfast account

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

Publish default Success, Cancelled, and Notify (ITN) views. This will also publish a Jetstream component that allows you to initiate a new subscription:

```bash
php artisan vendor:publish --provider="FintechSystems\Payfast\PayfastServiceProvider" --tag="payfast-views"
```
Optionally publish a Laravel Nova Subscription Resource to show resource events received via the callback

```bash
php artisan vendor:publish --provider="FintechSystems\Payfast\PayfastServiceProvider" --tag="payfast-nova-resource"
```

If you're using subscriptions, run the migrations:
```bash
php artisan migrate
```

## Livewire Setup

In `resources/views/profiles/show.php`, add the two Livewire components that displays subscriptions and receipts.

```
    <!-- Subscriptions -->
        <div class="mt-10 sm:mt-0">
            @livewire('payfast-jetstream-subscriptions')
        </div>
                
        <x-jet-section-border />
    <!-- End Subscriptions -->

    <!-- Receipts -->
        <div class="mt-10 sm:mt-0">
            @livewire('payfast-jetstream-receipts')
        </div>
    
    <x-jet-section-border />
    <!-- End Receipts -->
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

Route::get('/cancel-subscription', function() {
    return Payfast::cancelSubscription('73d2a218-695e-4bb5-9f62-383e53bef68f');
});

Route::get('/create-subscription', function() {
    return Payfast::createSubscription(
        Carbon::now()->addDay()->format('Y-m-d'),
        5, // Amount
        6 // Frequency (6 = annual, 3 = monthly)
    );
});

Route::get('/create-adhoc-token', function() {
    return Payfast::createAdhocToken(5);
});

Route::get('/fetch-subscription', function() {
    return Payfast::fetchSubscription('21189d52-12eb-4108-9c0e-53343c7ac692');
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

## Screenshots

[Livewire Subscriptions and Receipts Components](../screenshots/subscriptions_and_receipts.jpeg)

## Credits

- [Eugene van der Merwe](https://github.com/eugenevdm)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
