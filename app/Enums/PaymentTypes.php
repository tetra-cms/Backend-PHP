<?php

namespace App\Enums;

enum PaymentTypes: string
{
    case cash = 'CASH';
    case nonCash = 'NON_CASH';
}
