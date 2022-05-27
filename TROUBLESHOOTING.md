# Troubleshooting

## Console Error

```
Uncaught (in promise) TypeError: window.payfast_do_onsite_payment is not a function
```

Add this to app.blade.php

```
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