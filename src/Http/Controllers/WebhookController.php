<?php

namespace FintechSystems\Payfast\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use FintechSystems\Payfast\Cashier;
use Illuminate\Support\Facades\Log;
use FintechSystems\Payfast\Subscription;
use Symfony\Component\HttpFoundation\Response;
use FintechSystems\Payfast\Events\SubscriptionCreated;
use FintechSystems\Payfast\Events\SubscriptionCancelled;
use Laravel\Paddle\Exceptions\InvalidPassthroughPayload;

class WebhookController extends Controller
{    
    /**
     * Handle a Payfast webhook call.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function __invoke(Request $request)
    {
        $payload = $request->all();
        
        ray($payload);

        Log::debug($payload);

        if ($payload['custom_str1'] == 'subscription_created' && $payload['payment_status'] == Subscription::STATUS_DELETED) {
            $payload['custom_str1'] = 'subscription_cancelled';
        }
                   
        $method = 'handle'.Str::studly($payload['custom_str1']);

        ray($method);
        
        if (method_exists($this, $method)) {
            try {
                $this->{$method}($payload);
            } catch (InvalidPassthroughPayload $e) {
                return new Response('Webhook Skipped');
            }
            
            return new Response('Webhook Handled');
        }

        return new Response();
    }

    /**
     * Handle subscription created.
     *
     * @param  array  $payload
     * @return void
     *
     * @throws \Laravel\Paddle\Exceptions\InvalidPassthroughPayload
     */
    protected function handleSubscriptionCreated(array $payload)
    {        
        $customer = $this->findOrCreateCustomer($payload);

        $message = "handleSubscriptionCreated";

        ray($message);

        Log::debug($message);
        
        $subscription = $customer->subscriptions()->create([            
            'payfast_id' => $payload['token'],
            'name' => $payload['custom_str3'],
            'payfast_plan' => $payload['custom_int3'],
            'payfast_status' => $payload['payment_status'],
            'quantity' => $payload['custom_int4'],
        ]);
        
        SubscriptionCreated::dispatch($customer, $subscription, $payload);
    }

    /**
     * Handle subscription cancelled.
     *
     * @param  array  $payload
     * @return void
     */
    protected function handleSubscriptionCancelled(array $payload)
    {
        if (! $subscription = $this->findSubscription($payload['token'])) {
            $message = "Couldn't find the subscription to cancel";

            ray($message);

            return;
        }
        
        // Status...
        if (isset($payload['payment_status'])) {
            $subscription->payfast_status = $payload['payment_status'];
        }

        $subscription->paused_from = null;

        $subscription->save();

        SubscriptionCancelled::dispatch($subscription, $payload);
    }
    
    protected function findOrCreateCustomer(array $passthrough)
    {        
        if (! is_array($passthrough) || ! isset($passthrough['custom_int2'], $passthrough['custom_str2'])) {
            throw new InvalidPassthroughPayload;
        }

        return Cashier::$customerModel::firstOrCreate([
            'billable_id' => $passthrough['custom_int2'],
            'billable_type' => $passthrough['custom_str2'],
        ])->billable;
    }
    
    protected function findSubscription(string $subscriptionId)
    {
        return Cashier::$subscriptionModel::firstWhere('payfast_id', $subscriptionId);
    }

    /**
     * Determine if a receipt with a given Order ID already exists.
     *
     * @param  string  $orderId
     * @return bool
     */
    protected function receiptExists(string $orderId)
    {
        return Cashier::$receiptModel::where('order_id', $orderId)->count() > 0;
    }
}
