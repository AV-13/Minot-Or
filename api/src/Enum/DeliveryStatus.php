<?php
namespace App\Enum;

enum DeliveryStatus: string
{
    case InPreparation = 'in_preparation';
    case InProgress    = 'in_progress';
    case Delivered     = 'delivered';
}
