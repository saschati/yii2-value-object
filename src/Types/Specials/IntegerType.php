<?php
/**
 * PHP version 7.4
 *
 * @package Saschati\ValueObject\Types\Specials
 */

namespace Saschati\ValueObject\Types\Specials;

use Saschati\ValueObject\Types\Specials\Interfaces\SpecialInterface;

/**
 * Class IntegerType
 */
class IntegerType implements SpecialInterface
{


    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public static function convertToPhpValue($value)
    {
        return (int)$value;
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
