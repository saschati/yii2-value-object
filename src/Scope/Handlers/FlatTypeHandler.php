<?php
/**
 * PHP version 8.1
 *
 * @package Saschati\ValueObject\Scope\Handlers
 */

namespace Saschati\ValueObject\Scope\Handlers;

use Saschati\ValueObject\Scope\Handlers\Interfaces\HandlerInterface;
use Saschati\ValueObject\Types\Flats\Interfaces\FlatInterface;
use yii\db\ActiveRecordInterface;

/**
 * Class FlatTypeHandler
 *
 * This field serves as the content of all fields of special types
 * of the model which uses it with value 'attribute' => 'Type'.
 */
class FlatTypeHandler implements HandlerInterface
{
    /**
     * @param ActiveRecordInterface $model
     * @param string                $attribute
     * @param string|FlatInterface  $type
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

        $this->model->{$this->attribute} = $type::convertToPhpValue($value);
    }

    /**
     * @return void
     */
    public function normalize(): void
    {
        $type = $this->type;

        $this->model->{$this->attribute} = $type::convertToDatabaseValue($this->model->{$this->attribute});
    }
}
