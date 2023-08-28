[Back](../README.md)

Value Object data types, using [ValueObjectHandler](../src/Scope/Handlers/ValueObjectHandler.php)
================================================
This handler provides the ability to map database fields to Value Object.

Usage
-----
```php
use Saschati\ValueObject\Behaviors\ORMBehavior;
use Saschati\ValueObject\Types\ValueObjects\EmailType;
use Saschati\ValueObject\Types\ValueObjects\UuidType;
use Saschati\ValueObject\Helpers\TypeScope;
use Saschati\ValueObject\Scope\Handlers\ValueObjectHandler;
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
                    'id' => UuidType::class,
                    'email' => [
                        'scope' => TypeScope::VALUE_OBJECT_TYPE, // ValueObjectHandler::class
                        'type' => EmailType::class,
                        'reference' => 'db_email',
                    ],
                ],
            ],
        ];
    }
    ...
}
```
This handler will be applied if the key specification has a [ValueObjectInterface](../src/Types/ValueObjects/Interfaces/ValueObjectInterface.php) and "scope"
is specified as VALUE_OBJECT_TYPE in the array.

List of ValueObjectHandler properties
--------------
| name                                  | type                                                                                              | description                                                                                                                   |
|---------------------------------------|---------------------------------------------------------------------------------------------------|-------------------------------------------------------------------------------------------------------------------------------|
| type<span style="color:red">*</span>  | [::class](https://www.php.net/manual/en/language.oop5.basic.php#language.oop5.basic.class.class)  | Must be a valid type that extends [ValueObjectInterface](../src/Types/ValueObjects/Interfaces/ValueObjectInterface.php)       |
| reference                             | [@attribute, property, #virtual](../README.md#main-property)                                      | The attribute or property from which the ValueObject should be formed, and to which the value will be transferred when saved. |

List of existing ValueObjects
--------------
| type                                                                  | description                                                            |
|-----------------------------------------------------------------------|------------------------------------------------------------------------|
| [UuidType::class](../src/Types/ValueObjects/UuidType.php)             | Create id object for generate uuid with validation                     |
| [EmailType::class](../src/Types/ValueObjects/EmailType.php)           | Create email object for validate email                                 |
| [CollectionType::class](../src/Types/ValueObjects/CollectionType.php) | Create collection object from array fields on base "ramsey/collection" |

Base abstract class for custom ValueObject

| type                                                                    | description                                                                    |
|-------------------------------------------------------------------------|--------------------------------------------------------------------------------|
| [EnumType::class](../src/Types/ValueObjects/Abstracts/EnumType.php)     | Abstract class for create custom native type                                   |
| [IdType::class](../src/Types/ValueObjects/Abstracts/IdType.php)         | Abstract class for create custom ids, extends native type                      |
| [NativeType::class](../src/Types/ValueObjects/Abstracts/NativeType.php) | Abstract class for create custom native type                                   |
| [ArrayType::class](../src/Types/ValueObjects/Abstracts/ArrayType.php)   | Abstract class for create custom json filed ty Value Object type as one to one |

Create custom ValueObject
--------------
```php
use Saschati\ValueObject\Types\ValueObjects\Interfaces\ValueObjectInterface;
use Saschati\ValueObject\Behaviors\ORMBehavior;

class Name implements ValueObjectInterface
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
     * @param string $firstname
     * @param string $lastname
     */
    public function __construct(string $firstname, string $lastname)
    {
        $this->firstname = $firstname;
        $this->lastname  = $lastname;
    }

    /**
     * @return string
     */
    public function fullName(): string
    {
        return "{$this->firstname} {$this->lastname}";
    }

    /**
     * @return string
     */
    public function firstName(): string
    {
        return $this->firstname;
    }

    /**
     * @return string
     */
    public function lastName(): string
    {
        return $this->lastname;
    }
    
    /**
     * Named constructor to make a Value Object from a native value.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public static function convertToObjectValue($value): static
    {
        [$first, $last] = explode(' ', $value);
        
        return new self($first, $last);
    }

    /**
     * Returns the native value of this Value Object.
     *
     * @return mixed
     */
    public function convertToDatabaseValue(): string
    {
        return $this->fullName();
    }

    /**
     * Returns the string representation of this Value Object.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->fullName();
    }
}

class User extends ActiveRecord
{

    ...
    public function behaviors(): array
    {
        return [
            'vo' => [
                'class' => ORMBehavior::class,
                'attributes' => [
                    'full_name' => Name::class,
                ],
            ],
        ];
    }
    ...
}

$user = User::find()->one();

echo $user->full_name             // Alex Oleny
echo $user->full_name->fistName() // Alex
echo $user->full_name->lastName() // Oleny
```
Or create enum type (This is the old implementation, for the new one use trait [EnumType](../src/Traits/EnumType.php) with enum PHP8.1)
```php
use Saschati\ValueObject\Types\ValueObjects\Abstracts\EnumType;
use Saschati\ValueObject\Behaviors\ORMBehavior;

class Status implements EnumType
{
   
    public const WAIT    = 'wait';
    public const ACTIVE  = 'active';
    public const ARCHIVE = 'archive';

    /**
     * @return boolean
     */
    public function isWait(): bool
    {
        return $this->value === self::WAIT;
    }

    /**
     * @return boolean
     */
    public function isActive(): bool
    {
        return $this->value === self::ACTIVE;
    }
    
    /**
     * @return boolean
     */
    public function isArchive(): bool
    {
        return $this->value === self::ARCHIVE;
    }
}

class User extends ActiveRecord
{

    ...
    public function behaviors(): array
    {
        return [
            'vo' => [
                'class' => ORMBehavior::class,
                'attributes' => [
                    'status' => Status::class,
                ],
            ],
        ];
    }
    ...
}

$user = User::find()->one();

echo $user->status // wait

if ($user->status->isWait() === true) {
    $user->status = Status::ACTIVE();
}

echo $user->status // active
```