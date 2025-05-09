<?php
namespace App\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use App\Enum\ProductCategory;

class ProductCategoryType extends Type
{
    public const NAME = 'product_category_enum';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return "ENUM('flour','oil','egg','yeast','salt','sugar','butter','milk','seed','chocolate','bread')";
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?ProductCategory
    {
        return $value !== null ? ProductCategory::from($value) : null;
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
