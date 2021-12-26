<?php

namespace FintechSystems\Payfast;

use PayFast\PayFastApi as PayFastApiClient;
use FintechSystems\Payfast\Contracts\PaymentGateway;

class PayFastApi implements PaymentGateway
{    
    private $client;

    public function __construct(PayFastApiClient $client)
    {
        $this->client = $client;                
    }

    public function fetchSubscription($token)
    {
        $fetchArray = $this->client->subscriptions->fetch($token);

        return $fetchArray;
    }

}
