<?php

namespace FintechSystems\Payfast\Facades;

use Illuminate\Support\Facades\Facade;

class PayFastApi extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'payfast-api';
    }
}
