<?php
/**
 * PHP version 7.4
 *
 * @package Saschati\ValueObject\Types\ValueObjects\Abstracts
 */

namespace Saschati\ValueObject\Types\ValueObjects\Abstracts;

use InvalidArgumentException;
use Saschati\ValueObject\Types\ValueObjects\Interfaces\ValueObjectInterface;
use MabeEnum\Enum as BaseEnum;

use function strtolower;

/**
 * Class EnumType
 *
 * To implement Enum types for PHP version 8.1 below.
 */
abstract class EnumType extends BaseEnum implements ValueObjectInterface
{
    /**
     * @param mixed $value
     *
     * @return static
     */
    public static function convertToObjectValue($value): static
    {
        return static::get($value);
    }

    /**
     * @return mixed
     */
    public function convertToDatabaseValue(): mixed
    {
        return $this->getValue();
    }

    /**
     * Polyfill pseudo constructor for creating an object.
     *
     * @param mixed $value
     *
     * @return BaseEnum|null
     */
    public static function tryFrom(mixed $value): ?BaseEnum
    {
        if ($value === null) {
            return null;
        }

        try {
            return parent::get($value);
        } catch (InvalidArgumentException) {
            return null;
        }
    }

    /**
     * Polyfill pseudo constructor for creating an object.
     *
     * @param mixed $value
     *
     * @return BaseEnum
     */
    public static function from(mixed $value): BaseEnum
    {
        return parent::get($value);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return strtolower(parent::__toString());
    }
}
