<?php
/**
 * PHP version 8.1
 *
 * @package Saschati\ValueObject\Types\Specials
 */

namespace Saschati\ValueObject\Types\Flats;

use Saschati\ValueObject\Types\Flats\Interfaces\FlatInterface;

/**
 * Class BooleanType
 */
class BooleanType implements FlatInterface
{
    /**
     * @param mixed $value
     *
     * @return boolean
     */
    public static function convertToPhpValue(mixed $value): bool
    {
        return (bool)$value;
    }

    /**
     * @param mixed $value
     *
     * @return integer|null
     */
    public static function convertToDatabaseValue(mixed $value): ?int
    {
        if ($value === null) {
            return null;
        }

        return (int)$value;
    }
}
