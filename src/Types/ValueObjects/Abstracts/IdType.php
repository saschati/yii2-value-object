<?php
/**
 * PHP version 7.4
 *
 * @package Saschati\ValueObject\Types\ValueObjects\Abstracts
 */

namespace Saschati\ValueObject\Types\ValueObjects\Abstracts;

use Ramsey\Uuid\Uuid as BaseUuid;

/**
 * Class Id
 */
abstract class IdType extends NativeType
{

    /**
     * @return static
     *
     * @throws \Exception
     */
    public static function new()
    {
        return new static(BaseUuid::uuid4()->toString());
    }
}
