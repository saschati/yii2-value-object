<?php
/**
 * PHP version 8.1
 *
 * @package Saschati\ValueObject\Types\Specials
 */

namespace Saschati\ValueObject\Types\Flats;

use Saschati\ValueObject\Types\Flats\Interfaces\FlatInterface;

/**
 * Class StringType
 */
class StringType implements FlatInterface
{
    /**
     * @param mixed $value
     *
     * @return string
     */
    public static function convertToPhpValue(mixed $value): string
    {
        return (string)$value;
    }

    /**
     * @param mixed $value
     *
     * @return string|null
     */
    public static function convertToDatabaseValue(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return (string)$value;
    }
}
