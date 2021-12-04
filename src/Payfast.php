<?php

namespace FintechSystems\Payfast;

use PayFast\PayFastPayment;
use FintechSystems\Payfast\Contracts\PaymentGateway;

class Payfast implements PaymentGateway
{
    private $payment;

    private $returnUrl;

    private $cancelUrl;

    private $notifyUrl;

    public function __construct($client)
    {        
        ray($client);

        $this->payment = new PayFastPayment(
            [
                'merchantId' => $client['merchant_id'],
                'merchantKey' => $client['merchant_key'],
                'passPhrase' => $client['passphrase'],
                'testMode' => $client['testmode'],                
            ]
        );

        $this->returnUrl = $client['return_url'];
        $this->cancelUrl = $client['cancel_url'];
        $this->notifyUrl = $client['notify_url'];
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
}
