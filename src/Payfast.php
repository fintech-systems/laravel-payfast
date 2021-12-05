<?php

namespace FintechSystems\Payfast;

use PayFast\PayFastPayment;
use Illuminate\Support\Facades\Auth;
use FintechSystems\Payfast\Contracts\PaymentGateway;
use PayFast\PayFastApi;

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

    public function cancelSubscription($payfast_id) {
        $cancelArray = $this->api->subscriptions->cancel($payfast_id);

        ray($cancelArray);
        
        dd($cancelArray);
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
     * Set up an ad-hoc payment agreement
     * 
     * https://developers.payfast.co.za/docs#tokenization
     */
    public function createToken($amount = 0) {
        $data = [
            'custom_str1' => 'subscription_created',
            'subscription_type' => 2,
            'm_payment_id' => 'new_ad_hoc_agreement_' . Auth::user()->getKey(),
            'item_name' => 'Setup Subscription Agreement',            
            'item_description' => config('app.name') . ' Monthly Subscription',
            'amount' => $amount,
            'name_last' => Auth::user()->name,
            'email_address' => Auth::user()->email,                        
            'custom_str2' => Auth::user()->getMorphClass(),            
            'custom_str3' => config('app.name') . ' Monthly Subscription',
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
                'value' => 'Create Subscription',
                'class' => $this->cardUpdateLinkCss,
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
