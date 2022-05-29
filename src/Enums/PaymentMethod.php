<?php

namespace FintechSystems\Payfast\Enums;

class PaymentMethod
{
    public const CREDIT_CARD = "credit_card";
    public const EFT = "eft";

    public static function uiOptions()
    {
        return [
            self::CREDIT_CARD => 'Credit Card',
            self::EFT => 'EFT',
        ];
    }
}
