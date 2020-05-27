<?php
/**
 * PHP version 7.4
 *
 * @package Saschati\ValueObject\Types\ValueObjects
 */

namespace Saschati\ValueObject\Types\ValueObjects;

use Exception;
use Ramsey\Uuid\Uuid as BaseUuid;
use Saschati\ValueObject\Types\ValueObjects\Abstracts\IdType;
use Webmozart\Assert\Assert;

/**
 * Class Id
 */
class Id extends IdType
{


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
    public static function new()
    {
        return new static(BaseUuid::uuid4()->toString());
    }
}
