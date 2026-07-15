<?php

namespace App\Enums;

enum OrderStatus: string
{
    case PendingPayment = 'PENDING_PAYMENT';
    case InProgress = 'IN_PROGRESS';
    case Assembled = 'ASSEMBLED';
    case InDelivery = 'IN_DELIVERY';
    case Received = 'RECEIVED';
    case Cancelled = 'CANCELLED';
}
