<?php
namespace App\Enum;

enum SalesStatus: string
{
    case Pending            = 'pending';
    case PreparingProducts  = 'preparing_products';
    case AwaitingDelivery   = 'awaiting_delivery';
}
