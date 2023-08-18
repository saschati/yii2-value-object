<?php
/**
 * PHP version 8.1
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
     * @return static|null
     */
    public static function convertToObjectValue(mixed $value): ?static;

    /**
     * Returns the native value of this Value Object.
     *
     * @return mixed
     */
    public function convertToDatabaseValue(): mixed;

    /**
     * Returns the string representation of this Value Object.
     *
     * @return string
     */
    public function __toString();
}
