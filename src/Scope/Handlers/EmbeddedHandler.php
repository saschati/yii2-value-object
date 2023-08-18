<?php
/**
 * PHP version 8.1
 *
 * @package Saschati\ValueObject\Scope\Handlers
 */

namespace Saschati\ValueObject\Scope\Handlers;

use ReflectionClass;
use Saschati\ValueObject\Scope\Handlers\Interfaces\HandlerInterface;
use yii\db\ActiveRecordInterface;

/**
 * Class EmbeddedHandler
 *
 * 'attribute' => [
 *    'scope'  => TypeScope::EMBEDDED,
 *    'type'   => SomeClass::class,
 *    'mapper' => [
 *       'property1' => 'attribute1',
 *       'property2' => 'attribute2',
 *       ...
 *    ]
 * ]
 */
class EmbeddedHandler implements HandlerInterface
{
    /**
     * @param ActiveRecordInterface $model
     * @param string                $attribute
     * @param array                 $attributes
     * @param string                $class
     */
    public function __construct(
        private readonly ActiveRecordInterface $model,
        private readonly string $attribute,
        private readonly array $attributes,
        private readonly string $class
    ) {
    }

    /**
     * @return void
     * @throws \ReflectionException
     */
    public function cast(): void
    {
        $class      = $this->class;
        $attributes = $this->attributes;
        $model      = $this->model;
        $reflection = new ReflectionClass($class);

        $instance = $reflection->newInstanceWithoutConstructor();

        $mapper = (function () use ($attributes, $model) {
            foreach ($attributes as $property => $attribute) {
                $this->{$property} = $model->{$attribute};
            }
        })(...);

        $mapper->call($instance);

        $this->model->{$this->attribute} = $instance;
    }

    /**
     * @return void
     */
    public function normalize(): void
    {
        $attributes = $this->attributes;
        $model      = $this->model;
        $instance   = $model->{$this->attribute};

        $mapper = (function () use ($attributes, $model) {
            foreach ($attributes as $property => $attribute) {
                $model->{$attribute} = $this->{$property};
            }
        })(...);

        $mapper->call($instance);
    }
}
