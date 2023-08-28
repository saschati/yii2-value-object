<?php
/**
 * PHP version 8.1
 *
 * @package Saschati\ValueObject\Behaviors
 */

declare(strict_types=1);

namespace Saschati\ValueObject\Traits;

/**
 * Trait EnumType
 *
 * Support of enum type in PHP 8.1 as Value Object.
 */
trait EnumType
{
    /**
     * @return array
     */
    public static function getNames(): array
    {
        return array_column(self::cases(), 'name');
    }

    /**
     * @return array
     */
    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * @return array
     */
    public static function toArray(): array
    {
        return array_combine(self::getValues(), self::getNames());
    }

    /**
     * @param mixed $value
     *
     * @return static
     */
    public static function convertToPhpValue(mixed $value): static
    {
        return self::tryFrom($value);
    }

    /**
     * @param mixed $value
     *
     * @return string|integer|null
     */
    public static function convertToDatabaseValue(mixed $value): string|int|null
    {
        return $value?->value;
    }
}
