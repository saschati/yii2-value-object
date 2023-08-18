<?php
/**
 * PHP version 8.1
 *
 * @package Saschati\ValueObject\Types\Specials
 */

namespace Saschati\ValueObject\Types\Flats;

use Saschati\ValueObject\Types\Flats\Interfaces\FlatInterface;

/**
 * Class FloatType
 */
class FloatType implements FlatInterface
{
    /**
     * @param mixed $value
     *
     * @return float
     */
    public static function convertToPhpValue(mixed $value): float
    {
        return (float)$value;
    }

    /**
     * @param mixed $value
     *
     * @return float|null
     */
    public static function convertToDatabaseValue(mixed $value): ?float
    {
        if ($value === null) {
            return null;
        }

        return (float)$value;
    }
}
