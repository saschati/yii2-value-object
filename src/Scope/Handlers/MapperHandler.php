<?php
/**
 * PHP version 8.1
 *
 * @package Saschati\ValueObject\Scope\Handlers
 */

namespace Saschati\ValueObject\Scope\Handlers;

use Saschati\ValueObject\Scope\Handlers\Interfaces\HandlerInterface;
use yii\db\ActiveRecordInterface;

/**
 * Class MapperHandler
 *
 * 'mapper' => [
 *    'scope'  => TypeScope::MAPPER,
 *    'mapper' => [
 *       'property1' => 'attribute1',
 *       'property2' => 'attribute2',
 *       ...
 *    ]
 * ]
 */
class MapperHandler implements HandlerInterface
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

        $mapper = (function () use ($attributes) {
            foreach ($attributes as $property => $attribute) {
                $this->{$property} = $this->{$attribute};
            }
        })(...);

        $mapper->call($this->model);
    }

    /**
     * @return void
     */
    public function normalize(): void
    {
        $attributes = $this->attributes;

        $mapper = (function () use ($attributes) {
            foreach ($attributes as $property => $attribute) {
                $this->{$attribute} = $this->{$property};
            }
        })(...);

        $mapper->call($this->model);
    }
}
