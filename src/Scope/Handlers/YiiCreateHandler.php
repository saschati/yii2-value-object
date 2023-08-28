<?php
/**
 * PHP version 8.1
 *
 * @package Saschati\ValueObject\Scope\Handlers
 */

namespace Saschati\ValueObject\Scope\Handlers;

use Closure;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecordInterface;

use function array_combine;
use function array_keys;
use function array_map;

/**
 * Class YiiCreateHandler
 *
 * This handler works according to the principle of ConstructorHandler, but with the difference
 * that it creates an instance through the Yii::create() factory.
 *
 * @see Yii::createObject()
 *
 * @method void resolver(object|null $type, ActiveRecordInterface $model, AbstractHandler $handler) A mandatory
 * function that must map values from the type to the ActiveRecord model.
 * @method boolean nullIf(ActiveRecordInterface $model, AbstractHandler $handler) If the method returns true then null
 * will be passed to the specified property.
 *
 * Example:
 * 'attribute' => [
 *    'scope'  => TypeScope::YII_CREATE,
 *    'type'   => SomeClass::class,
 *    'params' => [
 *       'constructParam1' => 'attribute1',
 *       'constructParam2' => 'attribute2',
 *       'constructParam3' => 'no attribute value',
 *       ...
 *    ],
 *    'resolver' => static function (?SomeEntity $type, ActiveRecordInterface $model, ValueInterface $handler) {
 *        $model->attribute1 = $type->getProperty1Value();
 *        ...
 *    },
 *    'nullIf' => static function (ActiveRecordInterface $model, ValueInterface $handler) {
 *        return $model->attribute1 === null;
 *    },
 * ]
 */
class YiiCreateHandler extends AbstractHandler
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
     *
     * @throws InvalidConfigException
     */
    public function cast(): void
    {
        $class  = $this->class;
        $nullIf = $this->nullIf;
        $model  = $this->getModel();

        if ($nullIf !== null && $nullIf($model, $this) === true) {
            $this->setValue(null);

            return;
        }

        $keys = array_keys($this->params);

        $params = array_combine(
            $keys,
            array_map(
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

        $this->setValue(Yii::createObject($class, $params));
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
