<?php
/**
 * PHP version 8.1
 *
 * @package Saschati\ValueObject\Behaviors
 */

namespace Saschati\ValueObject\Behaviors;

use InvalidArgumentException;
use Saschati\ValueObject\Helpers\Flat;
use Saschati\ValueObject\Helpers\TypeScope;
use Saschati\ValueObject\Scope\Bugs\Interfaces\BugInterface;
use Saschati\ValueObject\Scope\Bugs\VirtualPropertyBug;
use Saschati\ValueObject\Scope\Handlers\ConstructorHandler;
use Saschati\ValueObject\Scope\Handlers\EachHandler;
use Saschati\ValueObject\Scope\Handlers\EmbeddedHandler;
use Saschati\ValueObject\Scope\Handlers\FlatTypeHandler;
use Saschati\ValueObject\Scope\Handlers\Interfaces\HandlerInterface;
use Saschati\ValueObject\Scope\Handlers\MapperHandler;
use Saschati\ValueObject\Scope\Handlers\ValueObjectHandler;
use Saschati\ValueObject\Scope\Handlers\YiiCreateHandler;
use Saschati\ValueObject\Types\Flats;
use Saschati\ValueObject\Types\Flats\Interfaces\FlatInterface;
use Saschati\ValueObject\Types\ValueObjects\Interfaces\ValueObjectInterface;
use Yii;
use yii\base\Behavior;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\db\BaseActiveRecord;

use function array_key_exists;
use function array_reverse;
use function class_exists;
use function class_implements;
use function in_array;
use function is_array;
use function is_callable;

/**
 * This behavior converts data from a database to custom types or value object
 * for easy use of classes instead of scalar types.
 *
 * Class ORMBehavior
 */
class ORMBehavior extends Behavior
{
    /**
     * A set of pre-harvested types, such as boolean, timestamp, etc.
     */
    private const SPECIAL_TYPE = [
        Flat::BOOLEAN_TYPE           => Flats\BooleanType::class,
        Flat::INTEGER_TYPE           => Flats\IntegerType::class,
        Flat::STRING_TYPE            => Flats\StringType::class,
        Flat::FLOAT_TYPE             => Flats\FloatType::class,
        Flat::JSON_TYPE              => Flats\JsonType::class,
        Flat::TIMESTAMP_TYPE         => Flats\TimestampType::class,
        Flat::TIMESTAMP_INTEGER_TYPE => Flats\TimestampIntegerType::class,
        Flat::SERIALIZE_TYPE         => Flats\SerializedType::class,
    ];

    /**
     * Mandatory field for the component in which you want to specify the dependencies of the model fields to its value
     *
     * 'attributes' => [
     *    'id'     => IdType::class,
     *    'email'  => EmailAddress::class,
     *    'active' => 'boolean',
     *    'name'   => [
     *        'scope'  => TypeScope::EMBEDDED,
     *        'type'   => SomeEmbeddedClass::class,
     *        'map' => [
     *           'firstName'  => 'first_name',
     *           'lastName'   => 'last_name',
     *           'middleName' => 'middle_name'
     *        ]
     *    ],
     * ].
     *
     * @var ValueObjectInterface[]
     */
    public array $attributes = [];

    /**
     * The public field which combines all user and default types of a plug-in
     * thereby unites them in a uniform array from which values will be taken further.
     *
     * @var FlatInterface[]
     */
    public array $specialTypes = [];

    /**
     * @var HandlerInterface[]
     */
    protected array $handlers = [];

    /**
     * @var boolean
     */
    protected bool $isDistributeTypes = false;

    /**
     * @var VirtualPropertyBug
     */
    protected BugInterface $virtualPropertyBug;

    /**
     * @var array
     */
    protected static array $implements = [];


    /**
     * @return void
     */
    public function init(): void
    {
        parent::init();

        $this->virtualPropertyBug = new VirtualPropertyBug();
    }

    /**
     * Array for event signature to be used
     *
     * @return array
     */
    public function events(): array
    {
        return [
            BaseActiveRecord::EVENT_AFTER_FIND    => 'cast',
            BaseActiveRecord::EVENT_BEFORE_INSERT => 'normalize',
            BaseActiveRecord::EVENT_BEFORE_UPDATE => 'normalize',
            BaseActiveRecord::EVENT_AFTER_INSERT  => 'recast',
            BaseActiveRecord::EVENT_AFTER_UPDATE  => 'recast',
        ];
    }

    /**
     * The basic data mapper method which develops and forms an array with all corresponding types,
     * all types which do not imitate the main interfaces, are simply passed.
     *
     * @return void
     *
     * @throws InvalidConfigException
     */
    public function cast(): void
    {
        $this->distributeTypes();

        foreach ($this->handlers as $handler) {
            $handler->setBug($this->virtualPropertyBug);
            $handler->cast();
        }
    }

    /**
     * @return void
     *
     * @throws InvalidConfigException
     */
    public function recast(): void
    {
        $this->distributeTypes();

        /** @var ActiveRecord $model */
        $model = $this->owner;

        $model->refresh();
    }

    /**
     * This method normalizes all the data to translate them into a database,
     * performs the appropriate methods for this in the boiler in the type interfaces.
     *
     * @return void
     *
     * @throws InvalidConfigException
     */
    public function normalize(): void
    {
        $this->distributeTypes();

        $handlers = $this->handlers;
        foreach (array_reverse($handlers) as $handler) {
            $handler->setBug($this->virtualPropertyBug);
            $handler->normalize();
        }
    }

    /**
     * This method splits and the group has data and pearl types
     * from the attributes field into two fields with ValueObject and SpecialTypes.
     *
     * @return void
     *
     * @throws InvalidArgumentException
     * @throws InvalidConfigException
     */
    protected function distributeTypes(): void
    {
        if ($this->isDistributeTypes === true) {
            return;
        }

        /** @var ActiveRecord $model */
        $model = $this->owner;

        $this->specialTypes = [
            ...self::SPECIAL_TYPE,
            ...$this->specialTypes,
        ];

        foreach ($this->attributes as $attribute => $type) {
            if (is_array($type) === true) {
                if (array_key_exists('scope', $type) === false) {
                    throw new InvalidArgumentException(
                        'Scope must be defined in the array for further processing.'
                    );
                }

                switch ($type['scope']) {
                    case TypeScope::VALUE_OBJECT_TYPE:
                    case ValueObjectHandler::class:
                        if ($this->isImplement(ValueObjectInterface::class, $type['type']) === false) {
                            throw new InvalidArgumentException(
                                'Value object type is not implement ValueObjectInterface.'
                            );
                        }

                        $this->handlers[] = new ValueObjectHandler(
                            $model,
                            $attribute,
                            $type['type'],
                            ($type['skipIfNull'] ?? true),
                            ($type['reference'] ?? null)
                        );
                        break;

                    case TypeScope::FLAT_TYPE:
                    case FlatTypeHandler::class:
                        if ($this->isImplement(FlatInterface::class, $type['type']) === false) {
                            throw new InvalidArgumentException(
                                'Flat type is not implement FlatInterface.'
                            );
                        }

                        $this->handlers[] = new FlatTypeHandler(
                            $model,
                            $attribute,
                            $type['type'],
                            ($type['reference'] ?? null)
                        );
                        break;

                    case TypeScope::CONSTRUCTOR:
                    case ConstructorHandler::class:
                        if (array_key_exists('type', $type) === false || class_exists($type['type']) === false) {
                            throw new InvalidArgumentException(
                                'The array must have a type for which you want to create a constructor.'
                            );
                        }

                        if (array_key_exists('params', $type) === false || is_array($type['params']) === false) {
                            throw new InvalidArgumentException('The constructor must have parameters.');
                        }

                        if (array_key_exists('resolver', $type) === false || is_callable($type['resolver']) === false) {
                            throw new InvalidArgumentException(
                                'You must pass a resolver to convert the new property object to entity attributes.'
                            );
                        }

                        $this->handlers[] = new ConstructorHandler(
                            $model,
                            $attribute,
                            $type['type'],
                            $type['params'],
                            $type['resolver'],
                            ($type['nullIf'] ?? null)
                        );
                        break;

                    case TypeScope::YII_CREATE:
                    case YiiCreateHandler::class:
                        if (array_key_exists('type', $type) === false || class_exists($type['type']) === false) {
                            throw new InvalidArgumentException(
                                'The array must have a type for which you want to create a Yii::create factory.'
                            );
                        }

                        if (array_key_exists('params', $type) === false || is_array($type['params']) === false) {
                            throw new InvalidArgumentException('Yii::create factory must have parameters.');
                        }

                        if (array_key_exists('resolver', $type) === false || is_callable($type['resolver']) === false) {
                            throw new InvalidArgumentException(
                                'You must pass a resolver to convert the new property object to entity attributes.'
                            );
                        }

                        $this->handlers[] = new YiiCreateHandler(
                            $model,
                            $attribute,
                            $type['type'],
                            $type['params'],
                            $type['resolver'],
                        );
                        break;

                    case TypeScope::EMBEDDED:
                    case EmbeddedHandler::class:
                        if (array_key_exists('type', $type) === false || class_exists($type['type']) === false) {
                            throw new InvalidArgumentException(
                                'The array must have a type for which you want to create a embedded class.'
                            );
                        }

                        if (array_key_exists('map', $type) === false || is_array($type['map']) === false) {
                            throw new InvalidArgumentException(
                                'You must populate the ActiveRecord property list with the Embedded class properties.'
                            );
                        }

                        $this->handlers[] = new EmbeddedHandler(
                            $model,
                            $attribute,
                            $type['map'],
                            $type['type'],
                            ($type['nullIf'] ?? null),
                            ($type['resolverIfNull'] ?? null),
                        );
                        break;

                    case TypeScope::MAPPER:
                    case MapperHandler::class:
                        if (array_key_exists('map', $type) === false || is_array($type['map']) === false) {
                            throw new InvalidArgumentException(
                                'You must populate the ActiveRecord property list with the mappers properties.'
                            );
                        }

                        $this->handlers[] = new MapperHandler($model, $type['map']);
                        break;

                    case TypeScope::EACH:
                    case EachHandler::class:
                        if (array_key_exists('type', $type) === false || class_exists($type['type']) === false) {
                            throw new InvalidArgumentException(
                                'The array must have a type for which you want to create a item class for each.'
                            );
                        }
                        $classType = EachHandler::TYPE_VO;
                        if ($this->isImplement(FlatInterface::class, $type['type']) === true) {
                            $classType = EachHandler::TYPE_FLAT;
                        }

                        $this->handlers[] = new EachHandler(
                            $model,
                            $type['type'],
                            $classType,
                            $attribute,
                            ($type['reference'] ?? null),
                        );
                        break;

                    default:
                        if ($this->isImplement(HandlerInterface::class, $type['scope']) === false) {
                            throw new InvalidArgumentException('Scope has an invalid definition.');
                        }

                        $this->handlers[] = Yii::createObject($type['scope'], [$model, $attribute, $type]);
                        break;
                }//end switch
            } else {
                if (class_exists($type) === true) {
                    if ($this->isImplement(FlatInterface::class, $type) === true) {
                        $this->handlers[] = new FlatTypeHandler($model, $attribute, $type);

                        continue;
                    }

                    if ($this->isImplement(ValueObjectInterface::class, $type) === true) {
                        $this->handlers[] = new ValueObjectHandler($model, $attribute, $type);

                        continue;
                    }
                }

                if (isset($this->specialTypes[$type]) === true) {
                    $this->handlers[] = new FlatTypeHandler($model, $attribute, $this->specialTypes[$type]);
                }
            }//end if
        }//end foreach

        $this->isDistributeTypes = true;
    }

    /**
     * @param string $interface
     * @param string $type
     *
     * @return boolean
     */
    protected function isImplement(string $interface, string $type): bool
    {
        return (in_array($interface, $this->getTypeImplements($type), true) === true);
    }

    /**
     * @param string $type
     *
     * @return array
     */
    protected function getTypeImplements(string $type): array
    {
        if (class_exists($type) === false) {
            return [];
        }

        return static::$implements[$type] ??= class_implements($type);
    }
}
