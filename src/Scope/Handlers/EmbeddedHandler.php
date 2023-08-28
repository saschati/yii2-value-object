<?php
/**
 * PHP version 8.1
 *
 * @package Saschati\ValueObject\Scope\Handlers
 */

namespace Saschati\ValueObject\Scope\Handlers;

use ReflectionClass;
use yii\db\ActiveRecordInterface;
use Closure;

/**
 * Class EmbeddedHandler
 *
 * This Handler maps defined properties or attributes or virtual properties to embedded classes as 1 to 1.
 *
 * @method void nullIf(ActiveRecordInterface $model, AbstractHandler $handler) If the method returns true then null
 * will be passed to the specified property.
 * @method boolean resolverIfNull(ActiveRecordInterface $model, AbstractHandler $handler) If this function is not
 * passed, then all keys specified in map will be specified as null
 *
 * Example:
 * 'attribute' => [
 *    'scope'  => TypeScope::EMBEDDED,
 *    'type'   => SomeClass::class,
 *    'map'    => [
 *       'property1' => 'attribute1',
 *       'property2' => 'attribute2',
 *       ...
 *    ],
 *    'nullIf' => static function (ActiveRecord $model, ValueInterface $handler) {
 *        return $model->attribute1 === null;
 *    },
 *    'resolverIfNull' => static function (ActiveRecord $model, ValueInterface $handler, array $attributes) {
 *        $model->attribute1 = null;
 *        ...
 *    },
 * ]
 */
class EmbeddedHandler extends AbstractHandler
{
    /**
     * @param ActiveRecordInterface $model
     * @param string                $attribute
     * @param array                 $attributes
     * @param string                $class
     * @param Closure|null          $nullIf
     * @param Closure|null          $resolverIfNull
     */
    public function __construct(
        private readonly ActiveRecordInterface $model,
        private readonly string $attribute,
        private readonly array $attributes,
        private readonly string $class,
        private readonly ?Closure $nullIf = null,
        private readonly ?Closure $resolverIfNull = null,
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
        $nullIf     = $this->nullIf;
        $model      = $this->getModel();

        if ($nullIf !== null && $nullIf($model, $this) === true) {
            $this->setValue(null);

            return;
        }

        $reflection = new ReflectionClass($class);
        $instance   = $reflection->newInstanceWithoutConstructor();

        $mapper = (function (AbstractHandler $them) use ($attributes, $model) {
            foreach ($attributes as $property => $attribute) {
                $this->{$property} = $them->getAttribute($model, $attribute);
            }
        })(...);

        $mapper->call($instance, $this);

        $this->setValue($instance);
    }

    /**
     * @return void
     */
    public function normalize(): void
    {
        $attributes = $this->attributes;
        $instance   = $this->getValue();
        $model      = $this->getModel();

        if ($instance === null) {
            $resolverIfNull = $this->resolverIfNull;
            if ($resolverIfNull !== null) {
                $resolverIfNull($model, $this, $attributes);
            } else {
                foreach ($attributes as $attribute) {
                    $this->setAttribute($model, $attribute, null);
                }
            }

            return;
        }

        $mapper = (function (AbstractHandler $them) use ($attributes, $model) {
            foreach ($attributes as $property => $attribute) {
                $them->setAttribute($model, $attribute, $this->{$property});
            }
        })(...);

        $mapper->call($instance, $this);
    }

    /**
     * @return ActiveRecordInterface
     */
    protected function getModel(): ActiveRecordInterface
    {
        return $this->model;
    }

    /**
     * @return string
     */
    protected function getProperty(): string
    {
        return $this->attribute;
    }
}
