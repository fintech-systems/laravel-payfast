<?php

namespace FintechSystems\Payfast;

use PayFast\PayFastPayment;
use FintechSystems\Payfast\Contracts\PaymentGateway;

class Payfast implements PaymentGateway
{
    private $payment;

    public function __construct($client)
    {
        $this->payment = new PayFastPayment(
            [
                'merchantId' => $client['merchant_id'],
                'merchantKey' => $client['merchant_key'],
                'passPhrase' => $client['passphrase'],
                'testMode' => $client['testmode']
            ]
        );
    }

    public function payment($amount, $itemName)
    {
        $data = [
            'amount' => $amount,
            'item_name' => $itemName
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
