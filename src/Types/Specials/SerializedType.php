<?php
/**
 * PHP version 7.4
 *
 * @package Saschati\ValueObject\Types\Specials
 */

namespace Saschati\ValueObject\Types\Specials;

use RuntimeException;
use Saschati\ValueObject\Types\Specials\Interfaces\SpecialInterface;

/**
 * Class ClassConservativeType
 */
class SerializedType implements SpecialInterface
{


    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public static function convertToPhpValue($value)
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_resource($value)) {
            $value = stream_get_contents($value);
        }

        $value = (string) $value;

        /**
         * @var array $val
         */
        $val = unserialize(quoted_printable_decode((string) json_decode($value, true)));

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException(json_last_error_msg());
        }

        return $val;
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    public static function convertToDatabaseValue($value)
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
