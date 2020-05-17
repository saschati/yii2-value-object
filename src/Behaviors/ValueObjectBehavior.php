<?php
/**
 * PHP version 7.4
 *
 * @package Saschati\ValueObject\Behaviors
 */

namespace Saschati\ValueObject\Behaviors;

use InvalidArgumentException;
use Saschati\ValueObject\Types\Specials;
use Saschati\ValueObject\Types\Specials\Interfaces\SpecialInterface;
use Saschati\ValueObject\Types\ValueObjects\Interfaces\ValueObjectInterface;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * Class ValueObjectBehavior
 */
class ValueObjectBehavior extends Behavior
{
    private const SPECIAL_TYPE = [
        'boolean'          => Specials\BooleanType::class,
        'integer'          => Specials\IntegerType::class,
        'string'           => Specials\StringType::class,
        'float'            => Specials\FloatType::class,
        'json'             => Specials\JsonType::class,
        'timestamp'        => Specials\TimestampType::class,
        'timestampInteger' => Specials\TimestampIntegerType::class,
    ];

    /**
     * @var SpecialInterface[]
     */
    public array $customSpecialTypes = [];

    /**
     * @var ValueObjectInterface[]
     */
    public array $attribute = [];

    /**
     * @var SpecialInterface[]
     */
    private array $specialTypes = [];

    /**
     * @var array[]
     */
    private array $attributeSpecialTypes = [];

    /**
     * @var array[]
     */
    private array $attributeValueObjects = [];


    /**
     * @return array
     */
    public function events(): array
    {
        return [
            ActiveRecord::EVENT_AFTER_FIND    => 'cast',
            ActiveRecord::EVENT_BEFORE_INSERT => 'normalize',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'normalize',
        ];
    }

    /**
     * @return void
     */
    public function cast()
    {
        /**
         * @var ActiveRecord $model
         */
        $model = $this->owner;

        $this->specialTypes          = $this->combineSpecialTypes();
        $this->attributeSpecialTypes = array_intersect($this->attribute, array_keys($this->specialTypes));

        if ($this->attributeSpecialTypes !== []) {
            foreach ($this->attributeSpecialTypes as $attribute => $type) {
                $model->{$attribute} = $this->castSpecialAttribute($type, $model->{$attribute});
            }
        }

        $this->attributeValueObjects = array_diff_key($this->attribute, $this->attributeSpecialTypes);

        if ($this->attributeValueObjects !== []) {
            foreach ($this->attributeValueObjects as $attribute => $type) {
                $model->{$attribute} = $this->castValueObjectAttribute($attribute, $model->{$attribute});
            }
        }
    }

    /**
     * @return void
     */
    public function normalize()
    {
        /**
         * @var ActiveRecord $model
         */
        $model = $this->owner;

        if ($this->attributeValueObjects !== []) {
            foreach ($this->attributeValueObjects as $attribute => $type) {
                $model->{$attribute} = $this->normalizeValueObjectAttribute($model->{$attribute});
            }
        }

        if ($this->attributeSpecialTypes !== []) {
            foreach ($this->attributeSpecialTypes as $attribute => $type) {
                $model->{$attribute} = $this->normalizeSpecialAttribute($type, $model->{$attribute});
            }
        }
    }

    /**
     * @param mixed $key
     * @param mixed $value
     *
     * @return mixed
     *
     * @throws InvalidArgumentException
     */
    protected function castValueObjectAttribute($key, $value)
    {
        if (true === empty($value)) {
            return null;
        }

        $castToClass = $this->getValueObjectCastType($key);

        if (null === $castToClass) {
            return $value;
        }

        if (false === in_array(ValueObjectInterface::class, class_implements($castToClass), true)) {
            throw new InvalidArgumentException($castToClass . ' not implement ' . ValueObjectInterface::class);
        }

        /**
         * @var $castToClass ValueObjectInterface
         */
        return $castToClass::convertToObjectValue($value);
    }

    /**
     * @param ValueObjectInterface|null $valueObject
     *
     * @return mixed
     *
     * @throws InvalidArgumentException
     */
    protected function normalizeValueObjectAttribute(?ValueObjectInterface $valueObject)
    {
        return (null !== $valueObject) ? $valueObject->convertToDatabaseValue() : null;
    }

    /**
     * @param string|SpecialInterface $type
     * @param mixed                   $value
     *
     * @return mixed
     */
    protected function castSpecialAttribute(string $type, $value)
    {
        if (class_exists($type) === true && $type instanceof SpecialInterface) {
            return $type::convertToPhpValue($value);
        }

        if (isset($this->specialTypes[$type]) === false && $this->specialTypes[$type] instanceof SpecialInterface) {
            throw new InvalidArgumentException("Type $type not found!");
        }

        return $this->specialTypes[$type]::convertToPhpValue($value);
    }

    /**
     * @param string $attribute
     * @param mixed  $value
     *
     * @return mixed
     */
    protected function normalizeSpecialAttribute(string $attribute, $value)
    {
        return $this->specialTypes[$attribute]::convertToDatabaseValue($value);
    }

    /**
     * @param mixed $key
     *
     * @return mixed|null
     */
    private function getValueObjectCastType($key)
    {
        if (true === isset($this->attribute[$key]) && true === class_exists($this->attribute[$key])) {
            return $this->attribute[$key];
        }

        return null;
    }

    /**
     * @return SpecialInterface[]
     */
    protected function combineSpecialTypes(): array
    {
        return ArrayHelper::merge(self::SPECIAL_TYPE, $this->customSpecialTypes);
    }
}
