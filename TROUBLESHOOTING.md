# Troubleshooting

## Naming conventions

PayFast is written camel case - variables are written so:

`payfastOptions`

## Payfast Website Links

Onsite payments reference:

- https://developers.payfast.co.za/docs#onsite_payments

### List of subscriptions

- https://my.payfast.co.za/transactions/customer-subscriptions

## How to setup local dev environment

Payfast heavily relies on a Webhook return so having non-public URLs like https://payfast.test won't work. You need to use something like `Expose` or `ngrok` to expose your local development environment.

If you're using `Expose`, this is how I set it up:

In `.env`:

```
APP_URL=https://payfast-test.test
#APP_URL=https://payfast.eu-1.sharedwithexpose.com
```

On the command line:

```
expose share --subdomain=payfast --server=eu-1 https://payfast-test.test
```

In my case I have the paid tier sparing me the schlep of always working with a new URL.

When testing, you can continue to use https://payfast-test.test instead of the Expose URL since Expose is only required for the webhook.

### Expose error `failed during DNS lookup`

With Expose, if you're using Linux and Laravel Valet for Linux and you get this error:

```
Connection to tls://eu-1.sharedwithexpose.com:443 failed during DNS lookup
```

Temporary comment out `127.0.0.1` in `/etc/resolv.conf` and try again.

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
composer require fintech-systems/laravel-payfast-onsite
```

## After Payfast returns, the Livewire subscription information isn't updated

Check inspect element to see if the event that tell the page to update is fired.

Make sure this stack command is in your `app.blade.php`, somewhere below `@livewireScripts`:

```blade
@stack('payfast-event-listener')
```

This is needed because an event listener is required before the Livewire subscription component will display the new subscription information. The event listener is:

```php
@push('payfast-event-listener')
    <script>        
    const refreshComponent = () => {
                console.log('Refreshing subscription status')
                Livewire.emit('billingUpdated')
            }
    </script>
@endpush
```

## window.payfast_do_onsite_payment is not a function Console Error

If you get the error below:

```
Uncaught (in promise) TypeError: window.payfast_do_onsite_payment is not a function
```

Add this to `app.blade.php`:

```html
<script src="https://www.payfast.co.za/onsite/engine.js" defer></script>
```

## Development Mode

If you're testing this solution with an existing project, do the following:

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

Then

```
composer require fintech-systems/laravel-payfast:dev-main
```

