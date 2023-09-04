<?php
/**
 * PHP version 8.1
 *
 * @package Saschati\ValueObject\Types\Specials
 */

namespace Saschati\ValueObject\Types\Flats;

use DateTimeImmutable;
use DateTimeInterface;
use Exception;
use Saschati\ValueObject\Types\Flats\Interfaces\FlatInterface;

/**
 * Class DateType
 *
 * Converting an date to a DateTimeImmutable.
 *
 * @see DateTimeImmutable
 */
class DateType implements FlatInterface
{
    /**
     * @param string|mixed $value
     *
     * @return DateTimeInterface|null
     *
     * @throws Exception
     */
    public static function convertToPhpValue(mixed $value): ?DateTimeInterface
    {
        if ($value === null) {
            return null;
        }

        return DateTimeImmutable::createFromFormat('!Y-m-d', $value);
    }

    /**
     * @param DateTimeInterface|mixed $value
     *
     * @return string|null
     */
    public static function convertToDatabaseValue($value): ?string
    {
        return $value?->format('Y-m-d');
    }
}
