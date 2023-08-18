<?php
/**
 * PHP version 7.4
 *
 * @package Saschati\ValueObject\Types\ValueObjects\Abstracts
 */

namespace Saschati\ValueObject\Types\ValueObjects\Abstracts;

use Saschati\ValueObject\Types\ValueObjects\Interfaces\ValueObjectInterface;
use MabeEnum\Enum as BaseEnum;

/**
 * Class EnumType
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
     * @param mixed $value
     *
     * @return BaseEnum
     */
    public static function set(mixed $value)
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
