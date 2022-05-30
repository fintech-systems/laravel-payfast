## About Laravel PayFast Onsite
![GitHub release (latest by date)](https://img.shields.io/github/v/release/fintech-systems/laravel-payfast-onsite) [![Build Status](https://app.travis-ci.com/fintech-systems/laravel-payfast-onsite.svg?branch=main)](https://app.travis-ci.com/fintech-systems/laravel-payfast-onsite) ![GitHub](https://img.shields.io/github/license/fintech-systems/laravel-payfast-onsite)

A [PayFast Onsite Payments](https://developers.payfast.co.za/docs#onsite_payments) implementation for Laravel designed to easy subscription billing. Livewire views are included.

** THIS IS BETA SOFTWARE **
There may be some bugs but the core functionality works.

Requirements:

- PHP 8.0
- Laravel
- A PayFast account

## Installation

Install the package via composer:

```bash
composer require fintech-systems/laravel-payfast-onsite
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

Run the migrations:
```bash
php artisan migrate
```

## Config Setup

The `config/payfast.php` holds key information to display subscriptions:

```php
<?php

return [
    'merchant_id' => env('PAYFAST_MERCHANT_ID', '10004002'),
    'merchant_key' => env('PAYFAST_MERCHANT_KEY', 'q1cd2rdny4a53'),
    'passphrase' => env('PAYFAST_PASSPHRASE', 'payfast'),
    'testmode' => env('PAYFAST_TESTMODE', true),        
    'return_url' => env('PAYFAST_RETURN_URL', config('app.url') . '/payfast/success'),
    'cancel_url' => env('PAYFAST_CANCEL_URL', config('app.url') . '/payfast/cancel'),
    'notify_url' => env('PAYFAST_NOTIFY_URL', config('app.url') . '/payfast/webhook'),
    'card_update_link_css' => env('CARD_UPDATE_LINK_CSS', 'inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition'),
    'card_updated_return_url' => env('CARD_UPDATED_RETURN_URL', config('app.url') . '/user/profile'),
    'plans' => [
        3 => [
            'name' => 'Monthly R 99',
            'start_date' => \Carbon\Carbon::now()->addDay()->format('Y-m-d'),
            'payfast_frequency' => 3,
            'initial_amount' => 5.01,
            'recurring_amount' => 5.02,
        ],
        6 => [
            'name' => 'Yearly R 1089',
            'start_date' => \Carbon\Carbon::now()->format('Y-m-d'),
            'payfast_frequency' => 6,
            'initial_amount' => 5.03,
            'recurring_amount' => 5.04,
        ]
    ],
    'cancelation_reasons' => [
        'Too expensive',
        'Lacks features',
        'Not what I expected',
    ],
];
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

![Livewire Subscriptions and Receipts Components](../../blob/main/screenshots/subscription_and_receipts.png)

## Credits

- [Eugene van der Merwe](https://github.com/eugenevdm)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
