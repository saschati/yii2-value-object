<?php
/**
 * PHP version 7.4
 *
 * @package Saschati\ValueObject\Types\Interfaces
 */

namespace Saschati\ValueObject\Types\ValueObjects\Interfaces;

/**
 * Interface ValueObjectInterface
 */
interface ValueObjectInterface
{


    /**
     * Named constructor to make a Value Object from a native value.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public static function convertToObjectValue($value);

    /**
     * Returns the native value of this Value Object.
     *
     * @return mixed
     */
    public function convertToDatabaseValue();

    /**
     * Returns the string representation of this Value Object.
     *
     * @return string
     */
    public function __toString();
}
