<?php
/**
 * PHP version 8.1
 *
 * @package Saschati\ValueObject\Scope\Handlers
 */

namespace Saschati\ValueObject\Scope\Handlers;

use Closure;
use Saschati\ValueObject\Scope\Handlers\Interfaces\HandlerInterface;
use yii\db\ActiveRecordInterface;

/**
 * Class ConstructorHandler
 *
 * 'attribute' => [
 *    'scope'  => TypeScope::CONSTRUCTOR,
 *    'type'   => SomeClass::class,
 *    'params' => [
 *       'attribute1',
 *       'attribute2',
 *       'no attribute value',
 *       ...
 *    ],
 *    'resolver' => static function (SomeEntity $type, ActiveRecord $model) {
 *        $model->attribute1 = $type->getProperty1Value();
 *        ...
 *    },
 * ]
 */
class ConstructorHandler implements HandlerInterface
{
    /**
     * @param ActiveRecordInterface $model
     * @param string                $attribute
     * @param string                $class
     * @param array                 $params
     * @param Closure               $resolver
     */
    public function __construct(
        private readonly ActiveRecordInterface $model,
        private readonly string $attribute,
        private readonly string $class,
        private readonly array $params,
        private readonly Closure $resolver,
    ) {
    }

    /**
     * @return void
     */
    public function cast(): void
    {
        $class = $this->class;
        $model = $this->model;

        $instance = new $class(
            ...array_map(
                static function (string $attribute) use ($model) {
                    if ($model->hasAttribute($attribute) === false) {
                        return $attribute;
                    }

                    return $model->{$attribute};
                },
                $this->params
            )
        );

        $this->model->{$this->attribute} = $instance;
    }

    /**
     * @return void
     */
    public function normalize(): void
    {
        $resolver = $this->resolver;

        $resolver($this->model->{$this->attribute}, $this->model);
    }
}
