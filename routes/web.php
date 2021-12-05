<?php

use Illuminate\Support\Facades\Route;

Route::post('/payfast/webhook', 'FintechSystems\Payfast\Http\Controllers\WebhookController')->name('webhook');

Route::get('/payfast/success', function() {
    return view('vendor.payfast.success');
});

Route::get('/payfast/cancel', function() {
    return view('vendor.payfast.cancel');
});