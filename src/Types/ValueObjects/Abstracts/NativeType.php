<?php
/**
 * PHP version 7.4
 *
 * @package Saschati\ValueObject\Types\ValueObjects\Abstracts
 */

namespace Saschati\ValueObject\Types\ValueObjects\Abstracts;

use Saschati\ValueObject\Types\ValueObjects\Interfaces\ValueObjectInterface;

/**
 * Class NativeType
 */
abstract class NativeType implements ValueObjectInterface
{

    /**
     * @var mixed
     */
    protected $value;


    /**
     * NativeType constructor.
     *
     * @param mixed $value
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($value)
    {
        if (true === empty($value)) {
            throw new \InvalidArgumentException('Empty value is not allow.');
        }

        $this->value = $value;
    }

    /**
     * Named constructor to instantiate a Value Object
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public static function convertToObjectValue($value)
    {
        return new static($value);
    }

    /**
     * Returns the raw $value
     *
     * @return mixed
     */
    public function convertToDatabaseValue()
    {
        return $this->value;
    }

    /**
     * Returns the raw $value
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Returns the string representation of $value
     *
     * @return string
     */
    public function __toString()
    {
        return (string)$this->getValue();
    }
}
