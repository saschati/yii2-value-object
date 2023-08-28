<?php
/**
 * PHP version 8.1
 *
 * @package Saschati\ValueObject\Types\ValueObjects\Abstracts
 */

namespace Saschati\ValueObject\Types\ValueObjects\Abstracts;

/**
 * Class Id
 *
 * An abstract identifier type for easy handling of identifiers regardless of their type.
 */
abstract class IdType extends NativeType
{
    /**
     * An abstract method that should return a new instance of the identifier.
     *
     * @return static
     */
    abstract public static function new(): static;
}
