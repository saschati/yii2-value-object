<?php
/**
 * PHP version 8.1
 *
 * @package Saschati\ValueObject\Helpers
 */

namespace Saschati\ValueObject\Helpers;

use InvalidArgumentException;

use function property_exists;

/**
 * Class Property
 */
class Property
{
    /**
     * @param object $instance
     * @param string $name
     *
     * @return mixed
     */
    public static function getValue(object $instance, string $name): mixed
    {
        $getter = function ($name) {
            /** @var object $reference */
            // phpcs:ignore Squiz.Scope.StaticThisUsage.Found
            $reference = $this;
            if (property_exists($reference, $name) === false) {
                throw new InvalidArgumentException('This class does not have such a property.');
            }

            return $reference->{$name};
        };

        return $getter->call($instance, $name);
    }

    /**
     * @param object $instance
     * @param string $name
     * @param mixed  $value
     *
     * @return mixed
     */
    public static function setValue(object $instance, string $name, mixed $value): mixed
    {
        $setter = function ($name, $value) {
            /** @var object $reference */
            // phpcs:ignore Squiz.Scope.StaticThisUsage.Found
            $reference = $this;
            if (property_exists($reference, $name) === false) {
                throw new InvalidArgumentException('This class does not have such a property.');
            }

            $reference->{$name} = $value;
        };

        return $setter->call($instance, $name, $value);
    }
}
