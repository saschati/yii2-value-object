<?php
/**
 * PHP version 8.1
 *
 * @package Saschati\ValueObject\Scope\Handlers
 */

namespace Saschati\ValueObject\Scope\Handlers;

use Saschati\ValueObject\Types\Flats\Interfaces\FlatInterface;
use yii\db\ActiveRecordInterface;

/**
 * Class FlatTypeHandler
 *
 * This field serves as the content of all fields of special types
 * of the model which uses it with value:
 * 'attribute' => SomeClass::class
 * or
 * 'attributeOrProperty' => [
 *    'scope'     => TypeScope::FLAT_TYPE,
 *    'type'      => SomeClass::class,
 *    'reference' => 'attribute',
 * ]
 */
class FlatTypeHandler extends AbstractHandler
{
    /**
     * @param ActiveRecordInterface $model
     * @param string                $attribute
     * @param string|FlatInterface  $type
     * @param string|null           $reference
     */
    public function __construct(
        private readonly ActiveRecordInterface $model,
        private readonly string $attribute,
        private readonly string $type,
        private readonly ?string $reference = null,
    ) {
    }

    /**
     * @return void
     */
    public function cast(): void
    {
        $value = $this->getValue();
        if ($value === null) {
            $this->setAttribute($this->getModel(), $this->attribute, null);

            return;
        }

        $type = $this->type;

        $this->setAttribute($this->getModel(), $this->attribute, $type::convertToPhpValue($value));
    }

    /**
     * @return void
     */
    public function normalize(): void
    {
        $type = $this->type;

        $this->setValue($type::convertToDatabaseValue($this->getAttribute($this->getModel(), $this->attribute)));
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
        return ($this->reference ?? $this->attribute);
    }
}
