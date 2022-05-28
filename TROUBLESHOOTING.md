# Troubleshooting

## How to setup local dev environment

Payfast heavily relies on a Webhook return so having non-public URLs like https://payfast.test won't work. You would need to use something like Expose from Beyond Code (or ngrok) to expose your local development environment.

If you're using Expose, this is how I set it up:

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

With Expose, if you're using Linux and Laravel Valet for Linux and you get this error:

```
Connection to tls://eu-1.sharedwithexpose.com:443 failed during DNS lookup
```

Temporary comment out `127.0.0.1` in `/etc/resolv.conf` and try again.

## After Payfast returns, the Livewire subscription information isn't updated

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

