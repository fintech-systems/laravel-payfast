<?php

namespace FintechSystems\Payfast\Enums;

class PaymentMethod
{
    const CREDIT_CARD = "credit_card";    
    const EFT = "eft";    
    
    public static function uiOptions()
    {
        return [
            self::CREDIT_CARD => 'Credit Card',
            self::EFT => 'EFT',
        ];
    }
}
