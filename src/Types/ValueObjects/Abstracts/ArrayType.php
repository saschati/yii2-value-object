<?php
/**
 * PHP version 8.1
 *
 * @package Saschati\ValueObject\Types\ValueObjects\Abstracts
 */

namespace Saschati\ValueObject\Types\ValueObjects\Abstracts;

use Saschati\ValueObject\Traits\ArrayToObject;
use Saschati\ValueObject\Traits\ObjectToArray;
use Saschati\ValueObject\Types\Flats\JsonType;
use Saschati\ValueObject\Types\ValueObjects\Interfaces\ValueObjectInterface;
use yii\base\InvalidConfigException;

use function array_filter;

/**
 * Class ArrayType
 *
 * Array type that assigns a flat mapper for array elements with 1 property to 1 key in the array.
 *
 * @see ArrayType::isClear() Determines whether to clear the array of null values before saving to the DB.
 */
abstract class ArrayType implements ValueObjectInterface
{
    use ArrayToObject;
    use ObjectToArray;


    /**
     * @param mixed|null|array $value
     *
     * @return static|null
     *
     * @throws InvalidConfigException
     */
    public static function convertToObjectValue(mixed $value): ?static
    {
        if ($value === null) {
            return null;
        }

        return static::createFromArray($value);
    }

    /**
     * @return array
     */
    public function convertToDatabaseValue(): array
    {
        return ($this->isClear() === true) ? array_filter($this->toArray()) : $this->toArray();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string)JsonType::convertToDatabaseValue($this->convertToDatabaseValue());
    }

    /**
     * Determines whether to clear the array of null values before saving to the DB.
     *
     * @return boolean
     */
    protected function isClear(): bool
    {
        return true;
    }
}
