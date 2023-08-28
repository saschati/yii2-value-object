[Back](../README.md)

Each item, using [EachHandler](../src/Scope/Handlers/EachHandler.php)
================================================
This handler can handle array elements by converting them to Value Object or FlatType.

Usage
-----
json:
```json
[
    {
        "phone": "phone1",
        "email": "email1",
        "address": "address1"
    },
    {
        "phone": "phone2",
        "email": "email2",
        "address": "address2"
    }
]
```
mapping:
```php
use Saschati\ValueObject\Behaviors\ORMBehavior;
use Saschati\ValueObject\Helpers\TypeScope;
use Saschati\ValueObject\Scope\Handlers\EachHandler;
use Saschati\ValueObject\Types\ValueObjects\Abstracts\ArrayType;
use Saschati\ValueObject\Types\Flats\JsonType;
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
                    'contacts' => JsonType::class,
                    'contacts' => [
                        'scope' => TypeScope::EACH, // EachHandler::class
                        'type' => Contact::class,
                    ],
                ]
            ],
        ];
    }
    ...
}

class Contact extends ArrayType
{
    /**
     * @var string 
     */
    private string $phone;
    
    /**
     * @var string 
     */
    private string $email;
    
    /**
     * @var string 
     */
    private string $address;
    
    
    ...
    /**
     * @return bool
     */
    protected function toJson(): bool
    {
        return false;
    }
}
```
This handler will be applied if "scope" EACH is specified in the array,
the library itself keeps track of property mapping.

List of EachHandler properties
--------------
| name                                 | type                                                                                             | description                                                                                                                                                                                 |
|--------------------------------------|--------------------------------------------------------------------------------------------------|---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| type<span style="color:red">*</span> | [::class](https://www.php.net/manual/en/language.oop5.basic.php#language.oop5.basic.class.class) | Must be a valid type that extends [ValueObjectInterface](../src/Types/ValueObjects/Interfaces/ValueObjectInterface.php) or [FlatInterface](../src/Types/Flats/Interfaces/FlatInterface.php) |
| reference                            | [@attribute, property, #virtual](../README.md#main-property)                                     | The attribute or property from which the ValueObject should be formed, and to which the value will be transferred when saved.                                                               |

Use CollectionType value object
---
Instead of this handler, you can use the much more convenient [CollectionType](../src/Types/ValueObjects/CollectionType.php) value object,
which creates a collection wrapper over the array using the "[ramsey/collection](https://github.com/ramsey/collection)" library.

Usage
```php
use Saschati\ValueObject\Behaviors\ORMBehavior;
use Saschati\ValueObject\Types\ValueObjects\Abstracts\ArrayType;
use Saschati\ValueObject\Types\ValueObjects\CollectionType;
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
                    'contacts' => Contacts::class,
                ]
            ],
        ];
    }
    ...
}

class Contacts extends CollectionType
{
    /**
     * 
     * The collection type that will be passed to the constructor
     * of this collection, if the collection type is VO or FT,
     * then the corresponding method will be called for each element of
     * the collection by receiving from the DB and before saving to the DB.
     * OR 
     * You can rewrite the methods at the bottom and convert the array however you
     * want without using Value Object or FlatType
     * 
     * @var string 
     */
    protected static string $type = Contact::class
    
    
    ...
//    /**
//     * IF YOU DON'T USE static::$type 
//     *
//     * Implement this method in the successor class to prepare each element of the collection
//     * before inserting it into the database
//     *
//     * @param mixed $item
//     *
//     * @return mixed
//     */
//    protected function preparedItemToDatabase(mixed $item): mixed
//    {
//        return $item;
//    }
//    
//    /**
//     * IF YOU DON'T USE static::$type 
//     *
//     * Implement this method in the descendant class to prepare each element of the collection
//     * before converting the array to a collection
//     *
//     * @param mixed $item
//     *
//     * @return mixed
//     */
//    protected static function preparedItemToObject(mixed $item): mixed
//    {
//        return $item;
//    }
}

class Contact extends ArrayType
{
    /**
     * @var string 
     */
    private string $phone;
    
    /**
     * @var string 
     */
    private string $email;
    
    /**
     * @var string 
     */
    private string $address;
    
    
    ...
    /**
     * @return bool
     */
    protected function toJson(): bool
    {
        return false;
    }
}
```