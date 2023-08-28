[Back](../README.md)

Embedded data types, using [EmbeddedHandler](../src/Scope/Handlers/EmbeddedHandler.php)
================================================
This handler provides an opportunity to map database fields with fields in embedded objects.

Usage
-----
```php
use Saschati\ValueObject\Behaviors\ORMBehavior;
use Saschati\ValueObject\Helpers\TypeScope;
use Saschati\ValueObject\Scope\Handlers\EmbeddedHandler;
...

class User extends ActiveRecord
{
    /**
     * @var Name 
     */
    private Name $name;
    
    /**
     * @var Token|null 
     */
    private ?Token $verifyEmail;

    ...
    public function behaviors(): array
    {
        return [
            'vo' => [
                'class' => ORMBehavior::class,
                'attributes' => [
                    'verify_email_life_at' => 'timestamp',
                    'name' => [
                        'scope' => TypeScope::EMBEDDED, // EmbeddedHandler::class
                        'type' => Name::class,
                        'map' => [
                            'firstname' => 'firstname',
                            'lastname' => 'lastname',
                            'middlename' => 'middlename',
                        ],
                    ],
                    'verifyEmail' => [
                        'scope' => TypeScope::EMBEDDED, // EmbeddedHandler::class
                        'type' => Token::class,
                        'map' => [
                            'token' => 'verify_email_token',
                            'lifeAt' => 'verify_email_life_at',
                        ],
                        'nullIf' => static fn (User $user): bool => ($user->verify_email_token === null)
                    ],
                ],
            ],
        ];
    }
    ...
}

class Token
{
    /**
     * @var string
     */
    private string $token;

    /**
     * @var string
     */
    private DateTimeInterface $lifeAt;
    ...
}

class Name
{
    /**
     * @var string
     */
    private string $firstname;

    /**
     * @var string
     */
    private string $lastname;

    /**
     * @var string
     */
    private string $middlename;
    ...
}
```

This handler will be applied if "scope" EMBEDDED is specified in the array,
the library itself keeps track of property mapping.

List of EmbeddedHandler properties
--------------
| name                                 | type                                                                                                                                                                                                                    | description                                                                                                                                                                                                                                          |
|--------------------------------------|-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| type<span style="color:red">*</span> | [::class](https://www.php.net/manual/en/language.oop5.basic.php#language.oop5.basic.class.class)                                                                                                                        | Can be any class with properties of various access modifiers                                                                                                                                                                                         |
| map<span style="color:red">*</span>  | array of key property and value [@attribute, property, #virtual](../README.md#main-property), `[property => @attribute]`                                                                                                | A property that indicates which properties from the embedded type class correspond to attributes from the model.                                                                                                                                     |
| nullIf                               | static function ([ActiveRecordInterface](https://www.yiiframework.com/doc/api/2.0/yii-db-activerecordinterface) $model, [EmbeddedHandler](../src/Scope/Handlers/EmbeddedHandler.php) $handler): bool                    | The property that receives the callback indicates whether the key of the array is a property with a value of this type, or whether it should be null at the stage of data mapping from the database.                                                 |
| resolverIfNull                       | static function ([ActiveRecordInterface](https://www.yiiframework.com/doc/api/2.0/yii-db-activerecordinterface) $model, [EmbeddedHandler](../src/Scope/Handlers/EmbeddedHandler.php) $handler, array $attributes): void | Additional processing in the event that the property to which the embedded class is mapped is null, and the user needs a departure from the default behavior of the logic. By default, all attributes specified in the map will also be set to null. |

Create custom Embedded class
---
You can create any embedded class with different nesting and packing of properties and with different access modifiers of the properties themselves, you can also use properties in the constructor because when the instance is created by the handler it will not be involved.
Please note that if the property to which the embedded class is assigned when stored is null, then all the attributes specified in the map will also be null.