<?php
namespace App\Enum;

enum OrderStatus: string
{
    case Pending           = 'pending';
    case PreparingProducts = 'preparing_products';
    case AwaitingDelivery  = 'awaiting_delivery';
    case Delivered         = 'delivered';
}
