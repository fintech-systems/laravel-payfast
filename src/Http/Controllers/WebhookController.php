<?php

namespace FintechSystems\Payfast\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use FintechSystems\Payfast\Cashier;
use FintechSystems\Payfast\Receipt;
use Illuminate\Support\Facades\Log;
use FintechSystems\Payfast\Subscription;
use FintechSystems\Payfast\Facades\Payfast;
use Symfony\Component\HttpFoundation\Response;
use FintechSystems\Payfast\Events\WebhookHandled;
use FintechSystems\Payfast\Events\WebhookReceived;
use FintechSystems\Payfast\Events\PaymentSucceeded;
use FintechSystems\Payfast\Events\SubscriptionCreated;
use FintechSystems\Payfast\Events\SubscriptionFetched;
use FintechSystems\Payfast\Events\SubscriptionCancelled;
use FintechSystems\Payfast\Exceptions\MissingSubscription;
use FintechSystems\Payfast\Events\SubscriptionPaymentSucceeded;
use FintechSystems\Payfast\Exceptions\MissingSubscriptionToken;
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
        Log::info("Incoming Webhook...");
        ray('Incoming Webook')->purple();
        $payload = $request->all();
        ray($payload)->blue();
        Log::debug($payload);

        WebhookReceived::dispatch($payload);
        
        try {
            if (!isset($payload['token'])) {
                $this->nonSubscriptionPaymentReceived($payload);
                WebhookHandled::dispatch($payload);            
                return new Response('Webhook nonSubscriptionPaymentReceived handled');
            }

            if (!$this->findSubscription($payload['token'])) {
                $this->createSubscription($payload);
                WebhookHandled::dispatch($payload);
                return new Response('Webhook createSubscription/applySubscriptionPayment handled');
            }

            if ($payload['payment_status'] == Subscription::PAYMENT_STATUS_CANCELLED) {
                $this->cancelSubscription($payload);
                WebhookHandled::dispatch($payload);
                return new Response('Webhook cancelSubscription handled');
            }

            if ($payload['payment_status'] == Subscription::PAYMENT_STATUS_COMPLETE) {
                $this->applySubscriptionPayment($payload);
                WebhookHandled::dispatch($payload);
                return new Response('Webhook applySubscriptionPayment handled');
            }
        } catch (Exception $e) {            
            $message = $e->getMessage();
            Log::critical($message);
            ray($message);
            ray($e);
            return new Response('Webhook Exception');
        }
        
    }

    /**
     * Handle one-time payment succeeded.
     *
     * @param  array  $payload
     * @return void
     */
    protected function nonSubscriptionPaymentReceived(array $payload)
    {        
        $message = "Creating a non-subscription payment receipt...";
        Log::info($message);
        ray($message)->orange();
        
        $receipt = Receipt::create([
            'merchant_payment_id' => $payload['m_payment_id'],
            'payfast_payment_id' => $payload['pf_payment_id'],
            'payment_status' => $payload['payment_status'],
            'item_name' => $payload['item_name'],
            'item_description' => $payload['item_description'],
            'amount_gross' => $payload['amount_gross'],
            'amount_fee' => $payload['amount_fee'],
            'amount_net' => $payload['amount_net'],
            'billable_id' => $payload['custom_int1'],
            'billable_type' => $payload['custom_str1'],
            'paid_at' => now(),
        ]);

        PaymentSucceeded::dispatch($receipt, $payload);        

        $message = "Created the non-subscription payment receipt.";
        Log::notice($message);
        ray($message)->green();        
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
            'merchant_payment_id' => $payload['m_payment_id'],
            'payment_status' => $payload['payment_status'],
            'status' => Subscription::STATUS_ACTIVE,            
            'next_bill_at' => $payload['billing_date'],
        ]);

        SubscriptionCreated::dispatch($customer, $subscription, $payload);

        $message = "Created a new subscription " . $payload['token'] . ".";
        Log::notice($message);
        ray($message)->green();

        $this->applySubscriptionPayment($payload);
    }

    /**
     * Apply a subscription payment succeeded.
     *
     * Gets triggered after first payment, and every subsequent payment that has a token
     *
     * @param  array  $payload
     * @return void
     */
    protected function applySubscriptionPayment(array $payload)
    {
        $message = "Applying a subscription payment to " . $payload['token'] . "...";
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

        $message = "Applied the subscription payment.";
        Log::notice($message);
        ray($message)->green();

        $this->fetchSubscriptionInformation($payload);
        // Fetch the subscription to update it's information

    }

    protected function fetchSubscriptionInformation(array $payload) {
        $message = "Fetching subscription information for " . $payload['token'] . "...";
        Log::info($message);
        ray($message)->orange();

        $result = Payfast::fetchSubscription($payload['token']);

        // Update or Create Subscription
        $subscription = Subscription::find(1);

        ray($result);

        SubscriptionFetched::dispatch($subscription, $payload);
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

        $message = "Cancelled the subscription.";
        Log::notice($message);
        ray($message)->green();
    }

    private function findSubscription(string $subscriptionId)
    {
        return Cashier::$subscriptionModel::firstWhere('token', $subscriptionId);
    }

    private function findOrCreateCustomer(array $passthrough)
    {
        if (!isset($passthrough['custom_str1'], $passthrough['custom_int1'])) {
            throw new InvalidMorphModelInPayload($passthrough['custom_str1'] . "|" . $passthrough['custom_int1']);
        }

        return Cashier::$customerModel::firstOrCreate([
            'billable_id' => $passthrough['custom_int1'],
            'billable_type' => $passthrough['custom_str1'],
        ])->billable;
    }
    
}
