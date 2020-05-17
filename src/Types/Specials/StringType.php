<?php
/**
 * PHP version 7.4
 *
 * @package Saschati\ValueObject\Types\Specials
 */

namespace Saschati\ValueObject\Types\Specials;

use Saschati\ValueObject\Types\Specials\Interfaces\SpecialInterface;

/**
 * Class StringType
 */
class StringType implements SpecialInterface
{


    /**
     * @param mixed $value
     *
     * @return string
     */
    public static function convertToPhpValue($value)
    {
        return (string)$value;
    }

    /**
     * @param $value
     *
     * @return string
     */
    public static function convertToDatabaseValue($value)
    {
        if ($value === null) {
            return null;
        }

        return (string)$value;
    }
}
