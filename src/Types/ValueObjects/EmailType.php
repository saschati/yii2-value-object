<?php
/**
 * PHP version 8.1
 *
 * @package Saschati\ValueObject\Types\ValueObjects
 */

namespace Saschati\ValueObject\Types\ValueObjects;

use Saschati\ValueObject\Types\ValueObjects\Abstracts\NativeType;
use Webmozart\Assert\Assert;

use function mb_strtolower;

/**
 * Class Email
 *
 * Implementation of email Value Object.
 */
class EmailType extends NativeType
{
    /**
     * Email constructor.
     *
     * @param string $value
     */
    public function __construct(string $value)
    {
        $value = mb_strtolower($value);

        Assert::email($value);

        parent::__construct($value);
    }
}
