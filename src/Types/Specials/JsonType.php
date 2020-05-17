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
 * Class JsonType
 */
class JsonType implements SpecialInterface
{


    /**
     * @param mixed $value
     *
     * @return boolean
     */
    public static function convertToPhpValue($value)
    {
        $value = json_decode($value, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException(json_last_error_msg());
        }

        return $value;
    }

    /**
     * @param $value
     *
     * @return integer
     */
    public static function convertToDatabaseValue($value)
    {
        if ($value === null) {
            return null;
        }

        $encoded = json_encode($value);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException(json_last_error_msg());
        }

        return $encoded;
    }
}
