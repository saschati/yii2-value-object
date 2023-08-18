<?php
/**
 * PHP version 8.1
 *
 * @package Saschati\ValueObject\Types\Specials
 */

namespace Saschati\ValueObject\Types\Flats;

use RuntimeException;
use Saschati\ValueObject\Types\Flats\Interfaces\FlatInterface;

/**
 * Class ClassConservativeType
 */
class SerializedType implements FlatInterface
{
    /**
     * @param mixed $value
     *
     * @return mixed
     *
     * @throws RuntimeException
     */
    public static function convertToPhpValue(mixed $value): mixed
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_resource($value) === true) {
            $value = stream_get_contents($value);
        }

        $value = (string)$value;

        /**
         * @var array $val
         */
        $val = unserialize(quoted_printable_decode((string)json_decode($value, true)));

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException(json_last_error_msg());
        }

        return $val;
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     *
     * @throws RuntimeException
     */
    public static function convertToDatabaseValue(mixed $value): mixed
    {
        if ($value === null) {
            return null;
        }

        $encoded = json_encode(quoted_printable_encode(serialize($value)));

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException(json_last_error_msg());
        }

        return $encoded;
    }
}
