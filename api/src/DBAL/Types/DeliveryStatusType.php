<?php
namespace App\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use App\Enum\DeliveryStatus;

class DeliveryStatusType extends Type
{
    public const NAME = 'delivery_status_enum';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return "ENUM('in_preparation','in_progress','delivered')";
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?DeliveryStatus
    {
        return $value !== null ? DeliveryStatus::from($value) : null;
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
