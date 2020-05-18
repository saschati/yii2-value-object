<?php
/**
 * PHP version 7.4
 *
 * @package Saschati\ValueObject\Types\Specials\Interfaces
 */

namespace Saschati\ValueObject\Types\Specials\Interfaces;

/**
 * Interface SpecialInterface
 */
interface SpecialInterface
{


    /**
     * Named constructor to make a Special Type from a native value.
     *
     * @param $value
     *
     * @return mixed
     */
    public static function convertToPhpValue($value);


    /**
     * Returns the native value of this Special Type.
     *
     * @param $value
     *
     * @return mixed
     */
    public static function convertToDatabaseValue($value);
}
