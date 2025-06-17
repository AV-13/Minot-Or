<?php
namespace App\Enum;

enum UserRole: string
{
    case WaitingForValidation = 'WaitingForValidation'; // A user who has registered but is waiting for account validation
    case Baker          = 'Baker';           // A bakery client who places orders via the web app
    case Sales          = 'Sales';           // A sales representative managing quotes, discounts, and supplier data
    case Driver         = 'Driver';          // A delivery driver using the mobile app to handle deliveries and scan QR codes
    case OrderPreparer  = 'OrderPreparer';   // A warehouse staff member preparing orders for delivery
    case Procurement    = 'Procurement';     // A procurement staff member managing stock levels and supplier transports
    case Maintenance    = 'Maintenance';    // A maintenance worker responsible for vehicle sanitation (e.g. cleaning tank trucks)
    // case Admin ?

    public function toSymfonyRole(): string
    {
        return 'ROLE_' . strtoupper($this->value);
    }
}
