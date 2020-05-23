<?php
/**
 * PHP version 7.4
 *
 * @package Saschati\ValueObject\Types\ValueObjects
 */

namespace Saschati\ValueObject\Types\ValueObjects;

use Saschati\ValueObject\Types\ValueObjects\Abstracts\IdType;
use Webmozart\Assert\Assert;

/**
 * Class Id
 */
class Id extends IdType
{

    public function __construct($value)
    {
        Assert::uuid($value);

        parent::__construct($value);
    }
}
