<?php
/**
 * PHP version 7.4
 *
 * @package Saschati\ValueObject\Types\ValueObjects
 */

namespace Saschati\ValueObject\Types\ValueObjects;

use Saschati\ValueObject\Types\ValueObjects\Abstracts\NativeType;

/**
 * Class Email
 */
class EmailAddress extends NativeType
{


    /**
     * Email constructor.
     *
     * @param $value
     */
    public function __construct($value)
    {
        parent::__construct($value);

        $filteredValue = filter_var($this->value, FILTER_VALIDATE_EMAIL);

        if (false === $filteredValue) {
            throw new \InvalidArgumentException("Invalid argument $value: Not an email address.");
        }

        $this->value = mb_strtolower($filteredValue);
    }
}
