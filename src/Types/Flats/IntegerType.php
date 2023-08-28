<?php
/**
 * PHP version 8.1
 *
 * @package Saschati\ValueObject\Types\Specials
 */

namespace Saschati\ValueObject\Types\Flats;

use Saschati\ValueObject\Types\Flats\Interfaces\FlatInterface;

/**
 * Class IntegerType
 *
 * Converting a value to an int.
 */
class IntegerType implements FlatInterface
{
    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public static function convertToPhpValue(mixed $value): int
    {
        return (int)$value;
    }

    /**
     * @param mixed $value
     *
     * @return integer|null
     */
    public static function convertToDatabaseValue(mixed $value): null|int
    {
        if ($value === null) {
            return null;
        }

        return (int)$value;
    }
}
