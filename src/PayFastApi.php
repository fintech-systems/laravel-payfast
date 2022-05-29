<?php

namespace FintechSystems\Payfast;

use FintechSystems\Payfast\Contracts\PaymentGateway;
use PayFast\PayFastApi as PayFastApiClient;

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
