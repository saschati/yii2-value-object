<?php
/**
 * PHP version 8.1
 *
 * @package Saschati\ValueObject\Scope\Handlers
 */

namespace Saschati\ValueObject\Scope\Handlers;

use Closure;
use Saschati\ValueObject\Scope\Handlers\Interfaces\HandlerInterface;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecordInterface;

/**
 * Class YiiCreateHandler
 *
 * 'attribute' => [
 *    'scope'  => TypeScope::YII_CREATE,
 *    'type'   => SomeClass::class,
 *    'params' => [
 *       'constructParam1' => 'attribute1',
 *       'constructParam2' => 'attribute2',
 *       'constructParam3' => 'no attribute value',
 *       ...
 *    ],
 *    'resolver' => static function (SomeEntity $type, ActiveRecord $model) {
 *        $model->attribute1 = $type->getProperty1Value();
 *        ...
 *    },
 * ]
 */
class YiiCreateHandler implements HandlerInterface
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
     *
     * @throws InvalidConfigException
     */
    public function cast(): void
    {
        $class = $this->class;
        $model = $this->model;

        $keys = array_keys($this->params);

        $params = array_combine(
            $keys,
            array_map(
                static function (string $attribute) use ($model) {
                    if ($model->hasAttribute($attribute) === false) {
                        return $attribute;
                    }

                    return $model->{$attribute};
                },
                $this->params
            )
        );

        $this->model->{$this->attribute} = Yii::createObject($class, $params);
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
