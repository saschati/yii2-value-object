<?php
/**
 * PHP version 8.1
 *
 * @package Saschati\ValueObject\Types\ValueObjects
 */

namespace Saschati\ValueObject\Types\ValueObjects;

use Exception;
use Ramsey\Uuid\Uuid as BaseUuid;
use Saschati\ValueObject\Traits\IsEqual;
use Saschati\ValueObject\Types\ValueObjects\Abstracts\IdType;
use Webmozart\Assert\Assert;

/**
 * Class Uuid
 *
 * Implementation of Id abstract type based on uuid Value Object for convenient use
 */
class UuidType extends IdType
{
    use IsEqual;


    /**
     * Id constructor.
     *
     * @param string $value
     */
    public function __construct(string $value)
    {
        Assert::uuid($value);

        parent::__construct($value);
    }

    /**
     * @return static
     *
     * @throws Exception
     */
    public static function new(): static
    {
        return new static(BaseUuid::uuid4()->toString());
    }
}
