<?php
use Doctrine\DBAL\Types\Type;
use App\DBAL\Types\{UserRoleType, TruckCategoryType, SalesStatusType, ProductCategoryType, OrderStatusType, DeliveryStatusType};

foreach ([
    UserRoleType::NAME => UserRoleType::class,
    TruckCategoryType::NAME => TruckCategoryType::class,
    SalesStatusType::NAME => SalesStatusType::class,
    ProductCategoryType::NAME => ProductCategoryType::class,
    OrderStatusType::NAME => OrderStatusType::class,
    DeliveryStatusType::NAME => DeliveryStatusType::class,
] as $name => $class) {
    if (!Type::hasType($name)) {
        Type::addType($name, $class);
    }
}
