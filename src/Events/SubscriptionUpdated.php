<?php

namespace FintechSystems\Payfast\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use FintechSystems\Payfast\Subscription;

class SubscriptionUpdated
{
    use Dispatchable, SerializesModels;

    /**
     * The subscription instance.
     *
     * @var \FintechSystems\Payfast\Subscription
     */
    public $subscription;

    /**
     * The webhook payload.
     *
     * @var array
     */
    public $payload;

    /**
     * Create a new event instance.
     *
     * @param  \FintechSystems\Payfast\Subscription  $subscription
     * @param  array  $payload
     * @return void
     */
    public function __construct(Subscription $subscription, array $payload)
    {
        $this->subscription = $subscription;
        $this->payload = $payload;
    }
}
