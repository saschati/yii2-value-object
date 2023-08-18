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
use yii\db\Expression;

/**
 * Class ArrayType
 */
abstract class ArrayType implements ValueObjectInterface
{
    use ArrayToObject;
    use ObjectToArray;

    /**
     * @param mixed $value
     *
     * @return static|null
     */
    public static function convertToObjectValue(mixed $value): ?static
    {
        $json = JsonType::convertToPhpValue($value);

        if ($json === null) {
            return null;
        }

        return static::createFromArray($json);
    }

    /**
     * @return array|Expression|null
     */
    public function convertToDatabaseValue(): array|Expression|null
    {
        $array = ($this->isClear() === true) ? array_filter($this->toArray()) : $this->toArray();

        return ($this->toJson() === true) ? JsonType::convertToDatabaseValue($array) : $array;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $array = ($this->isClear() === true) ? array_filter($this->toArray()) : $this->toArray();

        return JsonType::convertToDatabaseValue($array)->expression;
    }

    /**
     * @return boolean
     */
    protected function toJson(): bool
    {
        return true;
    }

    /**
     * @return boolean
     */
    protected function isClear(): bool
    {
        return true;
    }
}