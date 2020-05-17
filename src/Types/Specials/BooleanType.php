<?php
/**
 * PHP version 7.4
 *
 * @package Saschati\ValueObject\Types\Specials
 */

namespace Saschati\ValueObject\Types\Specials;

use Saschati\ValueObject\Types\Specials\Interfaces\SpecialInterface;

/**
 * Class BooleanType
 */
class BooleanType implements SpecialInterface
{


    /**
     * @param mixed $value
     *
     * @return boolean
     */
    public static function convertToPhpValue($value)
    {
        return (bool)$value;
    }

    /**
     * @param $value
     *
     * @return integer
     */
    public static function convertToDatabaseValue($value)
    {
        if ($value === null) {
            return null;
        }

        return (int)$value;
    }
}
