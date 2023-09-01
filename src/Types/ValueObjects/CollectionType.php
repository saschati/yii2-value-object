<?php
/**
 * PHP version 8.1
 *
 * @package App\Utils\ValueObject\Types
 */

namespace Saschati\ValueObject\Types\ValueObjects;

use Saschati\ValueObject\Types\Flats\Interfaces\FlatInterface;
use Saschati\ValueObject\Types\Flats\JsonType;
use Saschati\ValueObject\Types\ValueObjects\Interfaces\ValueObjectInterface;
use Saschati\ValueObject\Utils\Collection;

use function array_key_exists;
use function array_map;
use function class_exists;
use function class_implements;
use function in_array;

/**
 * Class CollectionType
 *
 * Value Object implementation of the collection type for working with arrays
 * with different data, by default the collection type is 'mixed'.
 */
class CollectionType extends Collection implements ValueObjectInterface
{
    /**
     * The collection type that will be passed to the constructor
     * of this collection, if the collection type is VO or FT,
     * then the corresponding method will be called for each element of
     * the collection by receiving from the DB and before saving to the DB.
     *
     * @var string|ValueObjectInterface|FlatInterface
     */
    protected static string $type = 'mixed';

    /**
     * List of implementations for caching.
     *
     * @var array
     */
    protected static array $classImplements = [];


    /**
     * Constructs a collection object of the specified type, optionally with the
     * specified data.
     *
     * @param array       $data           The initial items to store in the collection.
     * @param string|null $collectionType The type (FQCN) associated with this collection.
     */
    public function __construct(array $data = [], string $collectionType = null)
    {
        parent::__construct(($collectionType ?? static::$type), $data);
    }

    /**
     * Implement this method in the successor class to prepare each element of the collection
     * before inserting it into the database
     *
     * @param mixed $item
     *
     * @return mixed
     */
    protected function preparedItemToDatabase(mixed $item): mixed
    {
        if ($item === null) {
            return null;
        }

        if (static::isValueObjectType() === true) {
            return $item->convertToDatabaseValue();
        }

        if (static::isFlatType() === true) {
            return static::$type::convertToDatabaseValue($item);
        }

        return $item;
    }

    /**
     * Implement this method in the descendant class to prepare each element of the collection
     * before converting the array to a collection
     *
     * @param mixed $item
     *
     * @return mixed
     */
    protected static function preparedItemToObject(mixed $item): mixed
    {
        if ($item === null) {
            return null;
        }

        if (static::isValueObjectType() === true) {
            return static::$type::convertToObjectValue($item);
        }

        if (static::isFlatType() === true) {
            return static::$type::convertToPhpValue($item);
        }

        return $item;
    }

    /**
     * @param string|mixed|null $value
     *
     * @return static
     */
    public static function convertToObjectValue(mixed $value): static
    {
        $array = array_map([static::class, 'preparedItemToObject'], ($value ?? []));

        return new static($array, static::$type);
    }

    /**
     * @return array|null
     */
    public function convertToDatabaseValue(): ?array
    {
        $collection = array_map([$this, 'preparedItemToDatabase'], $this->toArray());

        return ($collection === []) ? null : $collection;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string)JsonType::convertToDatabaseValue($this->convertToDatabaseValue());
    }

    /**
     * @return boolean
     */
    protected static function isValueObjectType(): bool
    {
        return (static::$type !== 'mixed' && static::getItemClassImplement() === ValueObjectInterface::class);
    }

    /**
     * @return boolean
     */
    protected static function isFlatType(): bool
    {
        return (static::$type !== 'mixed' && static::getItemClassImplement() === FlatInterface::class);
    }

    /**
     * @return string
     */
    protected static function getItemClassImplement(): string
    {
        $class = static::$type;
        if (array_key_exists($class, static::$classImplements) === false) {
            if (class_exists($class) === false) {
                static::$classImplements[$class] = 'none';
            } else {
                $implements = class_implements($class);

                if (in_array(ValueObjectInterface::class, $implements, true) === true) {
                    static::$classImplements[$class] = ValueObjectInterface::class;
                } elseif (in_array(FlatInterface::class, $implements, true) === true) {
                    static::$classImplements[$class] = FlatInterface::class;
                } else {
                    static::$classImplements[$class] = 'none';
                }
            }
        }

        return static::$classImplements[$class];
    }
}
