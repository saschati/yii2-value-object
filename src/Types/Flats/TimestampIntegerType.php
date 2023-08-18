<?php
/**
 * PHP version 8.1
 *
 * @package Saschati\ValueObject\Types\Specials
 */

namespace Saschati\ValueObject\Types\Flats;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Exception;
use Saschati\ValueObject\Types\Flats\Interfaces\FlatInterface;

/**
 * Class TimestampIntegerType
 */
class TimestampIntegerType implements FlatInterface
{
    /**
     * @param integer $value
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

        return (new DateTimeImmutable())->setTimestamp($value);
    }

    /**
     * @param DateTime $value
     *
     * @return integer|null
     */
    public static function convertToDatabaseValue($value): ?int
    {
        return $value?->getTimestamp();
    }
}
