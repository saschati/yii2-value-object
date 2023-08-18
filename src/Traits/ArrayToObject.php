<?php
/**
 * PHP version 8.1
 *
 * @package App\Utils\ValueObject
 */

declare(strict_types=1);

namespace Saschati\ValueObject\Traits;

use Yii;
use yii\base\InvalidConfigException;

/**
 * Trait ArrayToObject
 */
trait ArrayToObject
{
    /**
     * @param array $data
     *
     * @return static
     *
     * @throws InvalidConfigException
     */
    public static function createFromArray(array $data): static
    {
        $object = Yii::createObject(static::class);

        $mapper = (function ($data) {
            $object = $this;

            foreach ($data as $key => $value) {
                if (property_exists($object, $key) === true) {
                    $object->{$key} = $value;
                }
            }
        })(...);

        $mapper->call($object, $data);

        return $object;
    }
}
