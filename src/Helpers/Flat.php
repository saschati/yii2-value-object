<?php
/**
 * PHP version 8.1
 *
 * @package Saschati\ValueObject\Helpers
 */

namespace Saschati\ValueObject\Helpers;

use Saschati\ValueObject\Types\Flats\BooleanType;
use Saschati\ValueObject\Types\Flats\DateTimeType;
use Saschati\ValueObject\Types\Flats\DateType;
use Saschati\ValueObject\Types\Flats\FloatType;
use Saschati\ValueObject\Types\Flats\IntegerType;
use Saschati\ValueObject\Types\Flats\JsonType;
use Saschati\ValueObject\Types\Flats\SerializedType;
use Saschati\ValueObject\Types\Flats\StringType;
use Saschati\ValueObject\Types\Flats\TimestampIntegerType;
use Saschati\ValueObject\Types\Flats\TimestampType;

/**
 * Class Flat
 */
class Flat
{
    /**
     * @see BooleanType
     */
    public const BOOLEAN_TYPE = 'boolean';
    /**
     * @see IntegerType
     */
    public const INTEGER_TYPE = 'integer';
    /**
     * @see StringType
     */
    public const STRING_TYPE = 'string';
    /**
     * @see FloatType
     */
    public const FLOAT_TYPE = 'float';
    /**
     * @see JsonType
     */
    public const JSON_TYPE = 'json';
    /**
     * @see TimestampType
     */
    public const TIMESTAMP_TYPE = 'timestamp';
    /**
     * @see TimestampIntegerType
     */
    public const TIMESTAMP_INTEGER_TYPE = 'timestamp:integer';
    /**
     * @see SerializedType
     */
    public const SERIALIZE_TYPE = 'serialized';
    /**
     * @see DateType
     */
    public const DATE_TYPE = 'date';
    /**
     * @see DateTimeType
     */
    public const DATE_TIME_TYPE = 'datetime';
}
