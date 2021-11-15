<?php


namespace Oesteve\Transformer;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor as BasePropertyAccessor;

class PropertyAccessor
{
    private static ?BasePropertyAccessor $propertyAccessor = null;

    public static function get(mixed $item, string $propertyName): string
    {
        if (static::$propertyAccessor === null) {
            static::$propertyAccessor = PropertyAccess::createPropertyAccessor();
        }

        $valueKey = static::$propertyAccessor->getValue($item, "[$propertyName]");

        if ($valueKey === null) {
            throw new UnableToDetermineItemKeyException();
        }

        return $valueKey;
    }
}
