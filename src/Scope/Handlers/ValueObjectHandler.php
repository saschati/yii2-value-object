<?php
/**
 * PHP version 8.1
 *
 * @package Saschati\ValueObject\Scope\Handlers
 */

namespace Saschati\ValueObject\Scope\Handlers;

use Saschati\ValueObject\Types\ValueObjects\Interfaces\ValueObjectInterface;
use yii\db\ActiveRecordInterface;

/**
 * Class ValueObjectHandler
 *
 * This field serves as the content of all fields of the value object
 * model that uses it with the value:
 * 'attribute' => 'Type'
 * or
 * 'attributeOrProperty' => [
 *    'scope'      => TypeScope::VALUE_OBJECT_TYPE,
 *    'type'       => SomeClass::class,
 *    'skipIfNull' => true,
 *    'reference'  => 'attribute',
 * ]
 */
class ValueObjectHandler extends AbstractHandler
{
    /**
     * @param ActiveRecordInterface       $model
     * @param string                      $attribute
     * @param string|ValueObjectInterface $type
     * @param boolean                     $skipIfNull
     * @param string|null                 $reference
     */
    public function __construct(
        private readonly ActiveRecordInterface $model,
        private readonly string $attribute,
        private readonly string $type,
        private readonly bool $skipIfNull = true,
        private readonly ?string $reference = null,
    ) {
    }

    /**
     * @return void
     */
    public function cast(): void
    {
        $value = $this->getValue();
        if ($value === null && $this->skipIfNull === true) {
            $this->setAttribute($this->getModel(), $this->attribute, null);

            return;
        }

        $type = $this->type;

        $this->setAttribute($this->getModel(), $this->attribute, $type::convertToObjectValue($value));
    }

    /**
     * @return void
     */
    public function normalize(): void
    {
        $this->setValue($this->getAttribute($this->getModel(), $this->attribute)?->convertToDatabaseValue());
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
