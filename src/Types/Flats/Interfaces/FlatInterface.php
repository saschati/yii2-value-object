<?php
/**
 * PHP version 8.1
 *
 * @package Saschati\ValueObject\Types\Specials\Interfaces
 */

namespace Saschati\ValueObject\Types\Flats\Interfaces;

/**
 * Interface FlatInterface
 */
interface FlatInterface
{
    /**
     * Named constructor to make a Special Type from a native value.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public static function convertToPhpValue(mixed $value): mixed;


    /**
     * Returns the native value of this Special Type.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public static function convertToDatabaseValue(mixed $value): mixed;
}
