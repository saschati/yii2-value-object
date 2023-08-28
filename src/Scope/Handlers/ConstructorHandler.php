<?php
/**
 * PHP version 8.1
 *
 * @package Saschati\ValueObject\Scope\Handlers
 */

namespace Saschati\ValueObject\Scope\Handlers;

use Closure;
use yii\db\ActiveRecordInterface;

use function array_map;

/**
 * Class ConstructorHandler
 *
 * This handler creates an object with constructor values that can be properties of the ActiveRecord model,
 * a "resolver" must be provided for data normalization.
 *
 * @method void resolver(object|null $type, ActiveRecordInterface $model, AbstractHandler $handler) A mandatory
 * function that must map values from the type to the ActiveRecord model.
 * @method boolean nullIf(ActiveRecordInterface $model, AbstractHandler $handler) If the method returns true then null
 * will be passed to the specified property.
 *
 * Example:
 * 'attribute' => [
 *    'scope'  => TypeScope::CONSTRUCTOR,
 *    'type'   => SomeClass::class,
 *    'params' => [
 *       'attribute1',
 *       'attribute2',
 *       'no attribute value',
 *       ...
 *    ],
 *    'resolver' => static function (SomeEntity|null $type, ActiveRecordInterface $model, ValueInterface $handler) {
 *        $model->attribute1 = $type->getProperty1Value();
 *        ...
 *    },
 *    'nullIf' => static function (ActiveRecordInterface $model, ValueInterface $handler) {
 *        return $model->attribute1 === null;
 *    },
 * ]
 */
class ConstructorHandler extends AbstractHandler
{
    /**
     * @param ActiveRecordInterface $model
     * @param string                $attribute
     * @param string                $class
     * @param array                 $params
     * @param Closure               $resolver
     * @param Closure|null          $nullIf
     */
    public function __construct(
        private readonly ActiveRecordInterface $model,
        private readonly string $attribute,
        private readonly string $class,
        private readonly array $params,
        private readonly Closure $resolver,
        private readonly ?Closure $nullIf = null,
    ) {
    }

    /**
     * @return void
     */
    public function cast(): void
    {
        $class  = $this->class;
        $model  = $this->model;
        $nullIf = $this->nullIf;

        if ($nullIf !== null && $nullIf($model, $this) === true) {
            $this->setValue(null);

            return;
        }

        $instance = new $class(
            ...array_map(
                function (string $attribute) use ($model) {
                    if ($this->isVirtual($attribute) === true) {
                        return $this->bug->get($attribute);
                    }

                    if ($model->hasAttribute($attribute) === false) {
                        return $attribute;
                    }

                    return $model->{$attribute};
                },
                $this->params
            )
        );

        $this->setValue($instance);
    }

    /**
     * @return void
     */
    public function normalize(): void
    {
        $resolver = $this->resolver;

        $resolver($this->getValue(), $this->getModel(), $this);
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
