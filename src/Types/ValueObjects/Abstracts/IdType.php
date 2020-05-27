<?php
/**
 * PHP version 7.4
 *
 * @package Saschati\ValueObject\Types\ValueObjects\Abstracts
 */

namespace Saschati\ValueObject\Types\ValueObjects\Abstracts;

/**
 * Class Id
 */
abstract class IdType extends NativeType
{


    /**
     * @return static
     */
    public abstract static function new();
}
