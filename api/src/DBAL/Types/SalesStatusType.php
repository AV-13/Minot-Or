<?php
namespace App\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use App\Enum\SalesStatus;

class SalesStatusType extends Type
{
    public const NAME = 'sales_status_enum';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return "ENUM('pending','preparing_products','awaiting_delivery')";
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?SalesStatus
    {
        return $value !== null ? SalesStatus::from($value) : null;
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
