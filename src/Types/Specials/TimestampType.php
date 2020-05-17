<?php
/**
 * PHP version 7.4
 *
 * @package Saschati\ValueObject\Types\Specials
 */

namespace Saschati\ValueObject\Types\Specials;

use DateTime;
use Exception;
use Saschati\ValueObject\Types\Specials\Interfaces\SpecialInterface;

/**
 * Class TimestampType
 */
class TimestampType implements SpecialInterface
{


    /**
     * @param string $value
     *
     * @return DateTime
     *
     * @throws Exception
     */
    public static function convertToPhpValue($value)
    {
        if ($value === null) {
            return null;
        }

        return DateTime::createFromFormat('Y-m-d H:i:s', $value);
    }

    /**
     * @param DateTime $value
     *
     * @return string
     */
    public static function convertToDatabaseValue($value)
    {
        if ($value === null) {
            return null;
        }

        return $value->format('Y-m-d H:i:s');
    }
}
