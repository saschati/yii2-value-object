<?php
/**
 * PHP version 7.4
 *
 * @package Saschati\ValueObject\Types\Specials
 */

namespace Saschati\ValueObject\Types\Specials;

use Saschati\ValueObject\Types\Specials\Interfaces\SpecialInterface;

/**
 * Class FloatType
 */
class FloatType implements SpecialInterface
{


    /**
     * @param mixed $value
     *
     * @return float
     */
    public static function convertToPhpValue($value)
    {
        return (float)$value;
    }

    /**
     * @param mixed $value
     *
     * @return float
     */
    public static function convertToDatabaseValue($value)
    {
        if ($value === null) {
            return null;
        }

        return (float)$value;
    }
}
