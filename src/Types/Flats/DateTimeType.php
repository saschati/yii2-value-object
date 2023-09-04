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
 * Class DateTimeType
 *
 * Converting an datetime to a DateTimeImmutable.
 *
 * @see DateTimeImmutable
 */
class DateTimeType extends TimestampType
{
}
