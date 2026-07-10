<?php

namespace App\Enums;

enum Role: string
{
    case USER = 'USER';
    case EMPLOYEE = 'EMPLOYEE';
    case ADMIN = 'ADMIN';
}
