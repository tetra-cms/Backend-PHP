<?php

namespace App\Enums;

enum PaymentTypes: string
{
    case Cash = 'CASH';
    case NonCash = 'NON_CASH';
}
