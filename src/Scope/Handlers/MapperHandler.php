<?php
/**
 * PHP version 8.1
 *
 * @package Saschati\ValueObject\Scope\Handlers
 */

namespace Saschati\ValueObject\Scope\Handlers;

use yii\db\ActiveRecordInterface;

/**
 * Class MapperHandler
 *
 * This handler exists for direct property mapping, useful if you need to create a
 * true private property for an entity from an attribute.
 *
 * 'mapper' => [
 *    'scope'  => TypeScope::MAPPER,
 *    'map'    => [
 *       'property1' => 'attribute1',
 *       'property2' => 'attribute2',
 *       ...
 *    ]
 * ]
 */
class MapperHandler extends AbstractHandler
{
    /**
     * @param ActiveRecordInterface $model
     * @param array                 $attributes
     */
    public function __construct(
        private readonly ActiveRecordInterface $model,
        private readonly array $attributes
    ) {
    }

    /**
     * @return void
     */
    public function cast(): void
    {
        $attributes = $this->attributes;

        $mapper = (function (AbstractHandler $them) use ($attributes) {
            /** @var ActiveRecordInterface $this */
            foreach ($attributes as $property => $attribute) {
                $them->setAttribute($this, $property, $them->getAttribute($this, $attribute));
            }
        })(...);

        $mapper->call($this->getModel(), $this);
    }

    /**
     * @return void
     */
    public function normalize(): void
    {
        $attributes = $this->attributes;

        $mapper = (function (AbstractHandler $them) use ($attributes) {
            /** @var ActiveRecordInterface $this */
            foreach ($attributes as $property => $attribute) {
                $them->setAttribute($this, $attribute, $them->getAttribute($this, $property));
            }
        })(...);

        $mapper->call($this->getModel(), $this);
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
        return '';
    }
}
