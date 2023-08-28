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
 * Class TimestampType
 *
 * Conversion of timestamp to DateTimeImmutable.
 *
 * @see DateTimeImmutable
 */
class TimestampType implements FlatInterface
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

        return DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $value);
    }

    /**
     * @param DateTimeInterface|mixed $value
     *
     * @return string|null
     */
    public static function convertToDatabaseValue(mixed $value): ?string
    {
        return $value?->format('Y-m-d H:i:s');
    }
}
