<?php
/**
 * PHP version 7.4
 *
 * @package Saschati\ValueObject\Behaviors
 */

namespace Saschati\ValueObject\Behaviors;

use InvalidArgumentException;
use Saschati\ValueObject\Helpers\Special;
use Saschati\ValueObject\Types\Specials;
use Saschati\ValueObject\Types\Specials\Interfaces\SpecialInterface;
use Saschati\ValueObject\Types\ValueObjects\Interfaces\ValueObjectInterface;
use yii\base\Behavior;
use yii\db\ActiveRecord;

/**
 * This behavior converts data from a database to custom types or value object
 * for easy use of classes instead of scalar types.
 *
 * Class MixedTypeBehavior
 */
class MixedTypeBehavior extends Behavior
{

    /**
     * A set of pre-harvested types, such as boolean, timestamp, etc.
     */
    private const SPECIAL_TYPE = [
        Special::BOOLEAN_TYPE           => Specials\BooleanType::class,
        Special::INTEGER_TYPE           => Specials\IntegerType::class,
        Special::STRING_TYPE            => Specials\StringType::class,
        Special::FLOAT_TYPE             => Specials\FloatType::class,
        Special::JSON_TYPE              => Specials\JsonType::class,
        Special::TIMESTAMP_TYPE         => Specials\TimestampType::class,
        Special::TIMESTAMP_INTEGER_TYPE => Specials\TimestampIntegerType::class,
        Special::SERIALIZE_TYPE         => Specials\SerializedType::class,
    ];

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
     * The private field which combines all user and default types of a plug-in
     * thereby unites them in a uniform array from which values ​​will be taken further.
     *
     * @var SpecialInterface[]
     */
    private array $specialTypes = [];

    /**
     * This field serves as the content of all fields of special types
     * of the model which uses it with value 'attribute' => 'Type'.
     *
     * @var array[]
     */
    private array $attributeSpecialTypes = [];

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
            ActiveRecord::EVENT_AFTER_INSERT  => 'afterSave',
            ActiveRecord::EVENT_AFTER_UPDATE  => 'afterSave',
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

        if ($this->attributeSpecialTypes !== []) {
            foreach ($this->attributeSpecialTypes as $attribute => $type) {
                $model->{$attribute} = $this->castSpecialAttribute($type, $model->{$attribute});
            }
        }

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
     * This method splits and the group has data and pearl types
     * from the attributes field into two fields with ValueObject and SpecialTypes.
     *
     * @return void
     */
    protected function distributeTypes(): void
    {
        $this->specialTypes = self::SPECIAL_TYPE;

        foreach ($this->attributes as $attribute => $type) {
            if (class_exists($type) === true) {
                if (in_array(SpecialInterface::class, class_implements($type)) === true) {
                    $this->attributeSpecialTypes[$attribute] = $type;

                    continue;
                }

                if (in_array(ValueObjectInterface::class, class_implements($type)) === true) {
                    $this->attributeValueObjects[$attribute] = $type;

                    continue;
                }
            }

            if (isset($this->specialTypes[$type]) === true) {
                if (in_array(SpecialInterface::class, class_implements($this->specialTypes[$type])) === true) {
                    $this->attributeSpecialTypes[$attribute] = $this->specialTypes[$type];
                }
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
     * Leads the castration of the scalar type in a special type.
     *
     * @param string|SpecialInterface $type
     * @param mixed                   $value
     *
     * @return mixed
     */
    protected function castSpecialAttribute($type, $value)
    {
        return $type::convertToPhpValue($value);
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

    /**
     * Normalization of special types in scalar type is carried out.
     *
     * @param SpecialInterface|string $type
     * @param mixed                   $value
     *
     * @return mixed
     */
    protected function normalizeSpecialAttribute(string $type, $value)
    {
        return $type::convertToDatabaseValue($value);
    }
}
