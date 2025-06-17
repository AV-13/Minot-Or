<?php
namespace App\DBAL\Types;

use App\Enum\TruckCategory;

class TruckCategoryType extends AbstractEnumType
{
    public const NAME = 'truck_category_enum';

    public function getName(): string
    {
        return self::NAME;
    }

    protected static function getEnumClass(): string
    {
        return TruckCategory::class;
    }
}
