<?php
/**
 * PHP version 8.1
 *
 * @package Saschati\ValueObject\Scope\Handlers
 */

namespace Saschati\ValueObject\Scope\Handlers;

use InvalidArgumentException;
use Saschati\ValueObject\Types\Flats\Interfaces\FlatInterface;
use Saschati\ValueObject\Types\ValueObjects\Interfaces\ValueObjectInterface;
use yii\db\ActiveRecordInterface;

use function array_map;
use function sprintf;

/**
 * Class EachHandler
 *
 * This Handler processes an array of values that are found by the attribute key or the "reference" value,
 * where the reference can be either a virtual type, a type from the db, or an object property.
 *
 * 'attributeOrProperty' => [
 *    'scope'     => TypeScope::EACH,
 *    'type'      => SomeType::class,
 *    'reference' => 'attribute',
 * ]
 */
class EachHandler extends AbstractHandler
{
    public const TYPE_VO   = 'vo';
    public const TYPE_FLAT = 'flat';


    /**
     * @param ActiveRecordInterface                            $model
     * @param string|ValueObjectInterface|FlatInterface|object $class
     * @param string                                           $type
     * @param string                                           $attribute
     * @param string|null                                      $reference
     */
    public function __construct(
        private readonly ActiveRecordInterface $model,
        private readonly string $class,
        private readonly string $type,
        private readonly string $attribute,
        private readonly ?string $reference = null,
    ) {
    }

    /**
     * @return void
     */
    public function cast(): void
    {
        $class = $this->class;
        $type  = $this->type;

        $items = array_map(
            static function (mixed $item) use ($class, $type) {
                if ($type === static::TYPE_VO) {
                    if ($item === null) {
                        return null;
                    }

                    /** @var ValueObjectInterface $class */
                    return $class::convertToObjectValue($item);
                }

                if ($type === static::TYPE_FLAT) {
                    if ($item === null) {
                        return null;
                    }

                    /** @var FlatInterface $class */
                    return $class::convertToPhpValue($item);
                }

                throw new InvalidArgumentException(
                    sprintf(
                        'Element type not defined, type must be a valid "%s" or "%s"',
                        ValueObjectInterface::class,
                        FlatInterface::class
                    )
                );
            },
            ($this->getValue() ?? [])
        );

        $this->setAttribute($this->getModel(), $this->attribute, $items);
    }

    /**
     * @return void
     */
    public function normalize(): void
    {
        $class = $this->class;
        $type  = $this->type;

        $items = array_map(
            static function (mixed $item) use ($class, $type) {
                if ($type === static::TYPE_VO) {
                    if ($item === null) {
                        return null;
                    }

                    /** @var ValueObjectInterface $item */
                    return $item->convertToDatabaseValue();
                }

                if ($type === static::TYPE_FLAT) {
                    if ($item === null) {
                        return null;
                    }

                    /** @var FlatInterface $class */
                    return $class::convertToDatabaseValue($item);
                }

                throw new InvalidArgumentException(
                    sprintf(
                        'Element type not defined, type must be a valid "%s" or "%s"',
                        ValueObjectInterface::class,
                        FlatInterface::class
                    )
                );
            },
            ($this->getAttribute($this->getModel(), $this->attribute) ?? [])
        );

        $this->setValue($items);
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
