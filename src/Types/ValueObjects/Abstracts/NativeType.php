<?php
/**
 * PHP version 8.1
 *
 * @package Saschati\ValueObject\Types\ValueObjects\Abstracts
 */

namespace Saschati\ValueObject\Types\ValueObjects\Abstracts;

use InvalidArgumentException;
use Saschati\ValueObject\Types\ValueObjects\Interfaces\ValueObjectInterface;

/**
 * Class NativeType
 *
 * An abstract native type for a convenient Value Object implementation on a single value.
 */
abstract class NativeType implements ValueObjectInterface
{
    /**
     * @var mixed
     */
    protected mixed $value;


    /**
     * NativeType constructor.
     *
     * @param mixed $value
     *
     * @throws InvalidArgumentException
     */
    public function __construct(mixed $value)
    {
        if (empty($value) === true) {
            throw new InvalidArgumentException('Empty value is not allow.');
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
    public static function convertToObjectValue(mixed $value): static
    {
        return new static($value);
    }

    /**
     * Returns the raw $value
     *
     * @return mixed
     */
    public function convertToDatabaseValue(): mixed
    {
        return $this->value;
    }

    /**
     * Returns the raw $value
     *
     * @return mixed
     */
    public function getValue(): mixed
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
