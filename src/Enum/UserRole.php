<?php
namespace App\Enum;

enum UserRole: string
{
    case Baker           = 'Baker';
    case Sales           = 'Sales';
    case Driver          = 'Driver';
    case OrderPreparer   = 'OrderPreparer';
    case Miller          = 'Miller';
    case Procurement     = 'Procurement';
}
