<?php

namespace FintechSystems\Payfast;

use FintechSystems\Payfast\Concerns\ManagesReceipts;
use FintechSystems\Payfast\Concerns\PerformsCharges;
use FintechSystems\Payfast\Concerns\ManagesCustomer;
use FintechSystems\Payfast\Concerns\ManagesSubscriptions;

trait Billable
{
    use ManagesCustomer;
    use ManagesSubscriptions;
    use ManagesReceipts;
    use PerformsCharges;

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
