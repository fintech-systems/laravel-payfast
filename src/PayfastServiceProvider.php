<?php

namespace FintechSystems\Payfast;

use FintechSystems\Payfast\Components\PayfastJetstreamSubscriptions;
use Livewire\Livewire;
use Illuminate\Support\ServiceProvider;

class PayfastServiceProvider extends ServiceProvider
{
    public function boot()
    {        
        $this->publishes([
            __DIR__.'/../config/payfast.php' => config_path('payfast.php'),
        ], 'payfast-config');
        
        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/payfast'),
        ], 'payfast-views');

        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        Livewire::component('payfast-jetstream-subscriptions', PayfastJetstreamSubscriptions::class);
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/payfast.php', 'payfast'
        );

        $this->app->bind('payfast', function () {
            return new Payfast([
                'merchant_id' => config('payfast.merchant_id'),
                'merchant_key' => config('payfast.merchant_key'),
                'passphrase' => config('payfast.passphrase'),
                'testmode' => config('payfast.testmode'),
                'return_url' => config('payfast.return_url'),
                'cancel_url' => config('payfast.cancel_url'),
                'notify_url' => config('payfast.notify_url'),
                'card_update_link_css' => config('payfast.card_update_link_css'),
                'card_updated_return_url' => config('payfast.card_updated_return_url'),
            ]);
        });
        
    }
}
