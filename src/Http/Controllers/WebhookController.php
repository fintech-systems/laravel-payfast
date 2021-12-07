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
use FintechSystems\Payfast\Events\WebhookHandled;
use FintechSystems\Payfast\Events\WebhookReceived;
use FintechSystems\Payfast\Events\SubscriptionCreated;
use FintechSystems\Payfast\Exceptions\NoTokenInPayload;
use FintechSystems\Payfast\Events\SubscriptionCancelled;
use FintechSystems\Payfast\Exceptions\MissingSubscription;
use FintechSystems\Payfast\Exceptions\InvalidWebhookCallback;
use FintechSystems\Payfast\Events\SubscriptionPaymentSucceeded;
use FintechSystems\Payfast\Exceptions\MissingSubscriptionToken;
use FintechSystems\Payfast\Exceptions\InvalidPassthroughPayload;
use FintechSystems\Payfast\Exceptions\InvalidMorphModelInPayload;

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
        
        if (!isset($payload['token'])) {
            throw new MissingSubscriptionToken;
        }

        WebhookReceived::dispatch($payload);

        if (!$this->findSubscription($payload['token'])) {
            $this->createSubscription($payload);
            WebhookHandled::dispatch($payload);
            return new Response('Webhook createSubscription/applySubscriptionPayment handled');
        }

        if (isset($payload['token']) && $payload['payment_status'] == Subscription::PAYMENT_STATUS_CANCELLED) {
            $this->cancelSubscription($payload);
            WebhookHandled::dispatch($payload);
            return new Response('Webhook cancelSubscription handled');
        }

        throw new InvalidWebhookCallback;
    }

    protected function createSubscription(array $payload)
    {
        $message = "Creating a new subscription...";
        Log::info($message);
        ray($message)->orange();

        $customer = $this->findOrCreateCustomer($payload);
        
        $subscription = $customer->subscriptions()->create([
            'token' => $payload['token'],
            'plan_id' => $payload['custom_int2'],
            'name' => $payload['custom_str2'],
            'payment_status' => $payload['payment_status'],
            'status' => Subscription::STATUS_ACTIVE,            
            'next_bill_at' => $payload['billing_date'],
        ]);

        SubscriptionCreated::dispatch($customer, $subscription, $payload);

        $message = "Created a new subscription " . $payload['token'];
        Log::notice($message);
        ray($message)->green();

        $this->applySubscriptionPayment($payload);
    }

    /**
     * Handle subscription payment succeeded.
     *
     * @param  array  $payload
     * @return void
     */
    protected function applySubscriptionPayment(array $payload)
    {
        $message = "Applying a payment to subscription " . $payload['token'] . "...";
        Log::info($message);
        ray($message)->orange();

        $billable = $this->findSubscription($payload['token'])->billable;

        $receipt = $billable->receipts()->create([
            'payfast_token' => $payload['token'],
            'payfast_payment_id' => $payload['pf_payment_id'],
            'order_id' => $payload['m_payment_id'],
            'amount' => $payload['amount_gross'],
            'fees' => $payload['amount_fee'],            
            'paid_at' => now(),
        ]);

        SubscriptionPaymentSucceeded::dispatch($billable, $receipt, $payload);

        $message = "Applied the payment";
        Log::notice($message);
        ray($message)->green();
    }

    /**
     * Handle subscription cancelled.
     *
     * @param  array  $payload
     * @return void
     */
    protected function cancelSubscription(array $payload)
    {
        $message = "Cancelling subscription " . $payload['token'] . "...";
        Log::info($message);
        ray($message)->orange();

        if (!$subscription = $this->findSubscription($payload['token'])) {
            throw new MissingSubscription;
        }
        
        // Cancellation date...
        if (is_null($subscription->ends_at)) {
            $subscription->ends_at = $subscription->onTrial()
                ? $subscription->trial_ends_at
                : $subscription->next_bill_at->subMinutes(1);
        }        
        
        $subscription->cancelled_at = now();

        $subscription->payment_status = $payload['payment_status'];

        $subscription->paused_from = null;

        $subscription->save();

        SubscriptionCancelled::dispatch($subscription, $payload);
    }

    protected function findOrCreateCustomer(array $passthrough)
    {
        if (!is_array($passthrough) || !isset($passthrough['custom_int1'], $passthrough['custom_str1'])) {
            throw new InvalidMorphModelInPayload;
        }

        return Cashier::$customerModel::firstOrCreate([
            'billable_id' => $passthrough['custom_int1'],
            'billable_type' => $passthrough['custom_str1'],
        ])->billable;
    }

    protected function findSubscription(string $subscriptionId)
    {
        return Cashier::$subscriptionModel::firstWhere('token', $subscriptionId);
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
