<?php
namespace App\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

abstract class AbstractEnumType extends Type
{
    abstract protected static function getEnumClass(): string;

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        $values = array_map(fn($val) => "'{$val}'", array_column(static::getEnumClass()::cases(), 'value'));
        return 'ENUM(' . implode(', ', $values) . ')';
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): mixed
    {
        $enumClass = static::getEnumClass();
        return $value !== null ? $enumClass::from($value) : null;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        return $value?->value;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
