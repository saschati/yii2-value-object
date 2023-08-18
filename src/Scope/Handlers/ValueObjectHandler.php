<?php
/**
 * PHP version 8.1
 *
 * @package Saschati\ValueObject\Scope\Handlers
 */

namespace Saschati\ValueObject\Scope\Handlers;

use Saschati\ValueObject\Scope\Handlers\Interfaces\HandlerInterface;
use Saschati\ValueObject\Types\ValueObjects\Interfaces\ValueObjectInterface;
use yii\db\ActiveRecordInterface;

/**
 * Class ValueObjectHandler
 *
 * This field serves as the content of all fields of the value object
 * model that uses it with the value 'attribute' => 'Type'.
 */
class ValueObjectHandler implements HandlerInterface
{
    /**
     * @param ActiveRecordInterface       $model
     * @param string                      $attribute
     * @param string|ValueObjectInterface $type
     */
    public function __construct(
        private readonly ActiveRecordInterface $model,
        private readonly string $attribute,
        private readonly string $type
    ) {
    }

    /**
     * @return void
     */
    public function cast(): void
    {
        $value = $this->model->{$this->attribute};
        if ($value === null) {
            $this->model->{$this->attribute} = null;

            return;
        }

        $type = $this->type;

        $this->model->{$this->attribute} = $type::convertToObjectValue($value);
    }

    /**
     * @return void
     */
    public function normalize(): void
    {
        $this->model->{$this->attribute} = $this->model->{$this->attribute}?->convertToDatabaseValue();
    }
}
