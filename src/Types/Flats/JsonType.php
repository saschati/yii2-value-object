<?php
/**
 * PHP version 8.1
 *
 * @package Saschati\ValueObject\Types\Specials
 */

namespace Saschati\ValueObject\Types\Flats;

use RuntimeException;
use Saschati\ValueObject\Types\Flats\Interfaces\FlatInterface;
use yii\db\Expression;

use function is_array;
use function json_decode;
use function json_encode;
use function json_last_error;
use function json_last_error_msg;

/**
 * Class JsonType
 *
 * Converting a value to an array from JSON.
 */
class JsonType implements FlatInterface
{
    /**
     * @param mixed $value
     *
     * @return array|null
     *
     * @throws RuntimeException
     */
    public static function convertToPhpValue(mixed $value): ?array
    {
        if ($value === null) {
            return null;
        }

        $value = (is_array($value) === true) ? $value : json_decode($value, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException(json_last_error_msg());
        }

        return $value;
    }

    /**
     * @param mixed $value
     *
     * @return Expression|null
     *
     * @throws RuntimeException
     */
    public static function convertToDatabaseValue(mixed $value): ?Expression
    {
        if ($value === null) {
            return null;
        }

        $encoded = json_encode($value, JSON_UNESCAPED_UNICODE);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException(json_last_error_msg());
        }

        return new Expression("'{$encoded}'");
    }
}
