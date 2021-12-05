<?php

namespace FintechSystems\Payfast;

// use Laravel\Paddle\Concerns\ManagesCustomer;
use Laravel\Paddle\Concerns\ManagesReceipts;
use Laravel\Paddle\Concerns\PerformsCharges;
use FintechSystems\Payfast\Concerns\ManagesCustomer;
use FintechSystems\Payfast\Concerns\ManagesSubscriptions;

trait Billable
{
    use ManagesCustomer;
    use ManagesSubscriptions;
    // use ManagesReceipts;
    // use PerformsCharges;

    /**
     * Get the default Paddle API options for the current Billable model.
     *
     * @param  array  $options
     * @return array
     */
    public function paddleOptions(array $options = [])
    {
        // return Cashier::paddleOptions($options);
    }
}
