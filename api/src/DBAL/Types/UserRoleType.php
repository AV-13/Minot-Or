<?php
namespace App\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use App\Enum\UserRole;

class UserRoleType extends Type
{
    public const NAME = 'user_role_enum';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return "ENUM('WaitingForValidation','Baker','Sales','Driver','OrderPreparer','Maintenance','Procurement')";
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?UserRole
    {
        return $value !== null ? UserRole::from($value) : null;
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
