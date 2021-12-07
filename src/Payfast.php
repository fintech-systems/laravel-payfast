<?php

namespace FintechSystems\Payfast;

use PayFast\PayFastApi;
use PayFast\PayFastPayment;
use FintechSystems\Payfast\Order;
use Illuminate\Support\Facades\Auth;
use FintechSystems\Payfast\Contracts\PaymentGateway;

class Payfast implements PaymentGateway
{
    private $payment;

    private $returnUrl;

    private $cancelUrl;

    private $notifyUrl;

    private $cardUpdateLinkCss;

    private $cardUpdatedReturnUrl;

    public function __construct($client)
    {
        $this->payment = new PayFastPayment(
            [
                'merchantId' => $client['merchant_id'],
                'merchantKey' => $client['merchant_key'],
                'passPhrase' => $client['passphrase'],
                'testMode' => $client['testmode'],
            ]
        );

        $this->api = new PayFastApi(
            [
                'merchantId' => $client['merchant_id'],
                'passPhrase' => $client['passphrase'],
                'testMode' => $client['testmode'],
                'custom_str1' => 'api',
            ]
        );

        $this->returnUrl = $client['return_url'];

        $this->cancelUrl = $client['cancel_url'];

        $this->notifyUrl = $client['notify_url'];

        $this->cardUpdateLinkCss = $client['card_update_link_css'];

        $this->cardUpdatedReturnUrl = $client['card_updated_return_url'];
    }

    public function cancelSubscription($token)
    {
        $cancelArray = $this->api->subscriptions->cancel($token);

        ray($cancelArray);
    }

    /**
     * Create a new subscription     
     */
    public function createSubscription($billingDate, $recurringAmount, $frequency, $cycles = 0)
    {
        $recurringType = Subscription::frequencies($frequency);

        $planId = match ($frequency) {
            6 => 1,
            3 => 2,
        };

        $order = Order::create([
            'billable_id' => Auth::user()->getKey(),
            'billable_type' => Auth::user()->getMorphClass(),
        ]);
        
        $data = [
            'custom_str1' => 'subscription',
            'm_payment_id' => $order->id,
            'subscription_type' => 1,
            'amount' => $recurringAmount + 1,
            'recurring_amount' => $recurringAmount,
            'billing_date' => $billingDate,
            'frequency' => $frequency,
            'cycles' => $cycles,

            'custom_int1' => Auth::user()->getKey(),
            'custom_str1' => Auth::user()->getMorphClass(),

            'custom_int2' => $planId,
            'custom_str2' => config('payfast.plans')[$planId],
                        
            'item_name' => config('app.name') . " $recurringType Subscription",
                                               
            'email_address' => Auth::user()->email,            
                        
            'return_url' => $this->returnUrl,
            'cancel_url' => $this->cancelUrl,
            'notify_url' => $this->notifyUrl,
        ];

        return $this->payment->custom->createFormFields(
            $data,
            [
                'value' => 'Create Subscription',
                'class' => $this->cardUpdateLinkCss,
            ]
        );
    }

    /**
     * Set up an ad-hoc payment agreement
     * 
     * https://developers.payfast.co.za/docs#tokenization
     */
    public function createToken($amount = 0)
    {
        $data = [
            'custom_str1' => 'subscription',
            'subscription_type' => 2,            
            'm_payment_id' => 'new_tokenization_' . Auth::user()->getKey(),
            'item_name' => config('app.name') . ' Monthly Subscription',
            'amount' => $amount,
            'name_last' => Auth::user()->name,
            'email_address' => Auth::user()->email,
            'custom_str2' => Auth::user()->getMorphClass(),
            'custom_str3' => 'Monthly Subscription',
            'custom_int2' => Auth::user()->getKey(),
            'custom_int3' => 1, // Plan ID
            'custom_int4' => 1, // Quantity                        
            // 'email_confirmation' => false,
            'return_url' => $this->returnUrl,
            'cancel_url' => $this->cancelUrl,
            'notify_url' => $this->notifyUrl,
        ];

        return $this->payment->custom->createFormFields(
            $data,
            [
                'value' => 'Create Tokenization',
                'class' => $this->cardUpdateLinkCss,
            ]
        );
    }

    public function fetchSubscription($token)
    {
        $fetchArray = $this->api->subscriptions->fetch($token);

        ray($fetchArray);
    }

    public function payment($amount, $itemName)
    {
        $data = [
            'amount' => $amount,
            'item_name' => $itemName,
            'return_url' => $this->returnUrl,
            'cancel_url' => $this->cancelUrl,
            'notify_url' => $this->notifyUrl,
        ];

        echo $this->payment->custom->createFormFields(
            $data,
            [
                'value' => 'PAY NOW',
                'class' => 'btn'
            ]
        );
    }

    /**
     * Generate a credit card update link
     * 
     * Add 'target' => '_blank' to open in new window
     */
    public function updateCardLink($token)
    {
        return $this->payment->custom->createCardUpdateLink(
            $token,
            $this->cardUpdatedReturnUrl,
            'Update Card',
            [
                'class' => $this->cardUpdateLinkCss,
            ]
        );
    }
}
