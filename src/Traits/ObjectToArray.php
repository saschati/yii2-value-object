<?php
/**
 * PHP version 8.1
 *
 * @package App\Utils\ValueObject
 */

declare(strict_types=1);

namespace Saschati\ValueObject\Traits;

use ReflectionClass;

/**
 * Class ObjectToArray
 */
trait ObjectToArray
{
    /**
     * @return array
     */
    public function toArray(): array
    {
        $reflection = new ReflectionClass($this);

        $mapper = (function ($properties) {
            $array = [];
            foreach ($properties as $property) {
                $name  = $property->getName();
                $value = $this->{$name};

                $array[$name] = $value;
            }

            return $array;
        })(...);

        return $mapper->call($this, $reflection->getProperties());
    }
}
