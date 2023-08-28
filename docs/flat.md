[Back](../README.md)

Flat data types, using [FlatTypeHandler](../src/Scope/Handlers/FlatTypeHandler.php)
================================================
This handler provides the ability to map database fields through converters that convert to what the user needs,
consider them as factories after find and before save.

Usage
-----
```php
use Saschati\ValueObject\Behaviors\ORMBehavior;
use Saschati\ValueObject\Types\Flats\BooleanType;
use Saschati\ValueObject\Types\Flats\TimestampType;
use Saschati\ValueObject\Helpers\TypeScope;
use Saschati\ValueObject\Scope\Handlers\FlatTypeHandler;
...

class User extends ActiveRecord
{
    ...
    public function behaviors(): array
    {
        return [
            'vo' => [
                'class' => ORMBehavior::class,
                'attributes' => [
                    'is_active'  => 'boolean', // BooleanType::class
                    'created_at' => [
                        'scope' => TypeScope::FLAT_TYPE, // FlatTypeHandler::class
                        'type' => TimestampType::class, // 'timestamp'
                        'reference' => 'created_at',
                    ],
                ],
            ],
        ];
    }
    ...
}
```
This handler will be applied if the key specification has a [FlatInterface](../src/Types/Flats/Interfaces/FlatInterface.php) and "scope"
is specified as FLAT_TYPE in the array.

List of FlatTypeHandler properties
--------------
| name                                  | type                                                                                                                   | description                                                                                                       |
|---------------------------------------|------------------------------------------------------------------------------------------------------------------------|-------------------------------------------------------------------------------------------------------------------|
| type<span style="color:red">*</span>  | [::class](https://www.php.net/manual/en/language.oop5.basic.php#language.oop5.basic.class.class), [alias](#flat-alias) | Must be a valid type that extends [FlatInterface](../src/Types/Flats/Interfaces/FlatInterface.php)                |
| reference                             | [@attribute, property, #virtual](../README.md#main-property)                                                           | The attribute or property from which to cast via FlatType, and to which the value will be transferred when saved. |

<a name="flat-alias"></a>List of existing FlatTypes
--------------
The list of available classes can be found at the [link](../src/Types/Flats).

| type                                                             | description                                                                                                                |
|------------------------------------------------------------------|----------------------------------------------------------------------------------------------------------------------------|
| [boolean](../src/Types/Flats/BooleanType.php)                    | Convert database value in php boolean                                                                                      |
| [integer](../src/Types/Flats/IntegerType.php)                    | Convert database value in php integer                                                                                      |
| [float](../src/Types/Flats/FloatType.php)                        | Convert database value in php float                                                                                        |
| [json](../src/Types/Flats/JsonType.php)                          | Convert database value as json in php assoc array                                                                          |
| [string](../src/Types/Flats/StringType.php)                      | Convert database value in php string                                                                                       |
| [timestamp](../src/Types/Flats/TimestampType.php)                | Convert database value as timestamp in php [\DateTimeImmutable](https://www.php.net/manual/ru/class.datetimeimmutable.php) |
| [timestamp:integer](../src/Types/Flats/TimestampIntegerType.php) | Convert database value as integer in php [\DateTimeImmutable](https://www.php.net/manual/ru/class.datetimeimmutable.php)   |
| [serialized](../src/Types/Flats/SerializedType.php)              | Convert database value as canned object in php object                                                                      |

Create custom FlatType
--------------
```php
use Saschati\ValueObject\Types\Flats\Interfaces\FlatInterface;
use Saschati\ValueObject\Behaviors\ORMBehavior;

class Money implements SpecialInterface
{
    /**
     * @param integer $value
     *
     * @return float
     */
    public static function convertToPhpValue($value): float
    {
        return ($value / 100);
    }

    /**
     * @param integer $value
     *
     * @return integer
     */
    public static function convertToDatabaseValue($value): int
    {
        return ($value * 100);
    }
}

class User extends ActiveRecord
{

    ...
    public function behaviors()
    {
        return [
            'st' => [
                'class'      => SpecialTypeBehavior::class,
                'attributes' => [
                    'money' => Money::class,
                ],
            ],
        ];
    }
    ...
}

// In database money equal 980
$user = User::find()->one();

echo $user->money // 9.8
```