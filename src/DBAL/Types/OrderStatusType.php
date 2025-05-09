<?php
namespace App\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use App\Enum\OrderStatus;

class OrderStatusType extends Type
{
    public const NAME = 'order_status_enum';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return "ENUM('pending','preparing_products','awaiting_delivery')";
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?OrderStatus
    {
        return $value !== null ? OrderStatus::from($value) : null;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        return $value?->value;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
