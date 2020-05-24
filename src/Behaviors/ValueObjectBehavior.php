<?php
/**
 * PHP version 7.4
 *
 * @package Saschati\ValueObject\Behaviors
 */

namespace Saschati\ValueObject\Behaviors;

use InvalidArgumentException;
use Saschati\ValueObject\Types\ValueObjects\Interfaces\ValueObjectInterface;
use yii\base\Behavior;
use yii\db\ActiveRecord;

/**
 * This behavior converts data from a database to custom types or value object
 * for easy use of classes instead of scalar types.
 *
 * Class ValueObjectBehavior
 */
class ValueObjectBehavior extends Behavior
{

    /**
     * Mandatory field for the component in which you want to specify the dependencies of the model fields to its value
     * 'attributes' => [
     *    'id'     => IdType::class,
     *    'email'  => EmailAddress::class,
     *    'status' => 'boolean',
     * ].
     *
     * @var ValueObjectInterface[]
     */
    public array $attributes = [];

    /**
     * This field serves as the content of all fields of the value object
     * model that uses it with the value 'attribute' => 'Type'.
     *
     * @var array[]
     */
    private array $attributeValueObjects = [];


    /**
     * Array for event signature to be used
     *
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
     * The basic data mapper method which develops and forms an array with all corresponding types,
     * all types which do not imitate the main interfaces, are simply passed.
     *
     * @return void
     */
    public function cast()
    {
        /**
         * @var ActiveRecord $model
         */
        $model = $this->owner;

        $this->distributeTypes();

        if ($this->attributeValueObjects !== []) {
            foreach ($this->attributeValueObjects as $attribute => $type) {
                $model->{$attribute} = $this->castValueObjectAttribute($type, $model->{$attribute});
            }
        }
    }

    /**
     * This method normalizes all the data to translate them into a database,
     * performs the appropriate methods for this in the boiler in the type interfaces.
     *
     * @return void
     */
    public function normalize(): void
    {
        /**
         * @var ActiveRecord $model
         */
        $model = $this->owner;

        $this->distributeTypes();

        if ($this->attributeValueObjects !== []) {
            foreach ($this->attributeValueObjects as $attribute => $type) {
                $model->{$attribute} = $this->normalizeValueObjectAttribute($model->{$attribute});
            }
        }
    }

    /**
     * This method splits and the group has data and pearl types
     * from the attributes field into two fields with ValueObject and SpecialTypes.
     *
     * @return void
     */
    protected function distributeTypes(): void
    {
        foreach ($this->attributes as $attribute => $type) {
            if (class_exists($type) === true && in_array(ValueObjectInterface::class, class_implements($type)) === true) {
                $this->attributeValueObjects[$attribute] = $type;
            }
        }
    }

    /**
     * Scalar type casting is performed in value object.
     *
     * @param ValueObjectInterface|string $castToClass
     * @param mixed                       $value
     *
     * @return mixed
     *
     * @throws InvalidArgumentException
     */
    protected function castValueObjectAttribute($castToClass, $value)
    {
        if (true === empty($value)) {
            return null;
        }

        if (null === $castToClass) {
            return $value;
        }

        return $castToClass::convertToObjectValue($value);
    }

    /**
     * Normalization of value object in scalar type is performed.
     *
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
}
