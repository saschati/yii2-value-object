Yii2 Value Object
=================
 This extension will help you work with your fields in the database as objects, selecting them from scalar values ​​in objects and vice versa. Also you will be able to make special types for on type json and in an array and to convert such scalar data as tinyint in bool.

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
composer require saschati/yii2-value-object "*"
```

or add

```
"saschati/yii2-value-object": "*"
```

to the require section of your `composer.json` file.


Usage
-----
You just need to connect one of the behavior variations to your ActiveRecord model
```
use Saschati\ValueObject\Behaviors\ValueObjectBehavior;
use Saschati\ValueObject\Types\ValueObjects\EmailAddress;
use Saschati\ValueObject\Types\ValueObjects\Id;

class User extends ActiveRecord
{

    ...
    public function behaviors()
    {
        return [
            'vo' => [
                'class'      => ValueObjectBehavior::class,
                'attributes' => [
                    'id'    => Id::class,
                    'email' => EmailAddress::class,
                ],
            ],
        ];
    }
    ...
}
```
Or special type
```
use Saschati\ValueObject\Behaviors\SpecialTypeBehavior;
use Saschati\ValueObject\Types\Specials\BooleanType;
use Saschati\ValueObject\Types\Specials\TimestampType;

class User extends ActiveRecord
{

    ...
    public function behaviors()
    {
        return [
            'st => [
                'class'      => SpecialTypeBehavior::class,
                'attributes' => [
                    'status'     => 'boolean',//BooleanType::class
                    'created_at' => 'timestamp',//TimestampType::class
                ],
            ],
        ];
    }
    ...
}
```
Or mixed behavior that includes functionality and **ValueObjectBehavior** and **SpecialTypeBehavior**
```

use Saschati\ValueObject\Behaviors\MixedTypeBehavior;
use Saschati\ValueObject\Types\ValueObjects\EmailAddress;
use Saschati\ValueObject\Types\Specials\BooleanType;
use Saschati\ValueObject\Types\Specials\JsonType;

class User extends ActiveRecord
{

    ...
    public function behaviors()
    {
        return [
            'mt' => [
                'class'      => MixedTypeBehavior::class,
                'attributes' => [
                    'status'     => BooleanType::class,
                    'created_at' => 'timestamp',
                    'email'      => EmailAddress::class,
                    'json_data'  => JsonType::class,
                ],
            ],
        ];
    }
    ...
}
```
List of existing SpecialTypes
--------------
| type             | description                                                                                                        |
|------------------|--------------------------------------------------------------------------------------------------------------------|
| boolean          | Convert database value in php boolean, class Saschati\ValueObject\Types\Specials\BooleanType                       |
| integer          | Convert database value in php integer, class Saschati\ValueObject\Types\Specials\IntegerType                       |
| float            | Convert database value in php float, class Saschati\ValueObject\Types\Specials\FloatType                           |
| json             | Convert database value as json in php assoc array, class Saschati\ValueObject\Types\Specials\JsonType              |
| string           | Convert database value in php string, class Saschati\ValueObject\Types\Specials\StringType                         |
| timestamp        | Convert database value as timestamp in php \DateTime, class Saschati\ValueObject\Types\Specials\TimestampType      |
| timestampInteger | Convert database value as integer in php \DateTime, class Saschati\ValueObject\Types\Specials\TimestampIntegerType |
| serialized       | Convert database value as canned object in php object, class Saschati\ValueObject\Types\Specials\SerializedType    |

List of existing ValueObjects
--------------
| type                | description                                                                                           |
|---------------------|-------------------------------------------------------------------------------------------------------|
| Id::class           | Create id object for generate uuid with validation, class Saschati\ValueObject\Types\ValueObjects\Id  |
| EmailAddress::class | Create email object for validate email, class Saschati\ValueObject\Types\ValueObjects\EmailAddress    |

base abstract class for custom ValueObject

| type              | description                                                                                                               |
|-------------------|---------------------------------------------------------------------------------------------------------------------------|
| EnumType::class   | Abstract class for create custom native type, class Saschati\ValueObject\Types\ValueObjects\Abstracts\NativeType          |
| IdType::class     | Abstract class for create custom ids, extends native type, class Saschati\ValueObject\Types\ValueObjects\Abstracts\IdType |
| NativeType::class | Abstract class for create custom native type, class Saschati\ValueObject\Types\ValueObjects\Abstracts\NativeType          |

Create custom ValueObject
--------------
```
use Saschati\ValueObject\Types\ValueObjects\Interfaces\ValueObjectInterface;
use Saschati\ValueObject\Behaviors\ValueObjectBehavior;

class Name implements ValueObjectInterface
{
    /**
     * @var string
     */
    private string $first;

    /**
     * @var string
     */
    private string $last;


    /**
     * @param string $first
     * @param string $last
     */
    public function __construct(string $first, string $last)
    {
        $this->first = $first;
        $this->last  = $last;
    }

    /**
     * @return string
     */
    public function fullName()
    {
        return "$this->first $this->last";
    }

    /**
     * @return string
     */
    public function firstName()
    {
        return $this->first;
    }

    /**
     * @return string
     */
    public function lastName()
    {
        return $this->last;
    }
    
    /**
     * Named constructor to make a Value Object from a native value.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public static function convertToObjectValue($value) 
    {
        list($first, $last) = explode(' ', $value);
        
        return new self($first, $last);
    }

    /**
     * Returns the native value of this Value Object.
     *
     * @return mixed
     */
    public function convertToDatabaseValue()
    {
        return $this->fullName();
    }

    /**
     * Returns the string representation of this Value Object.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->fullName();
    }
}

class User extends ActiveRecord
{

    ...
    public function behaviors()
    {
        return [
            'vo' => [
                'class'      => ValueObjectBehavior::class,
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
or
```
use Saschati\ValueObject\Types\ValueObjects\Abstracts\EnumType;
use Saschati\ValueObject\Behaviors\ValueObjectBehavior;

class Status implements EnumType
{
   
    public const WAIT    = 'wait';
    public const ACTIVE  = 'active';
    public const ARCHIVE = 'archive';

    /**
     * @return boolean
     */
    public function isWait()
    {
        return $this->value === self::WAIT;
    }

    /**
     * @return boolean
     */
    public function isActive()
    {
        return $this->value === self::ACTIVE;
    }
    
    /**
     * @return boolean
     */
    public function isArchove()
    {
        return $this->value === self::ARCHIVE;
    }
}

class User extends ActiveRecord
{

    ...
    public function behaviors()
    {
        return [
            'vo' => [
                'class'      => ValueObjectBehavior::class,
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

if (true === $user->status->isWait()) {
    $user->status = Status::ACTIVE();
}

echo $user->status // active
```
Create custom SpecialType
--------------
```
use Saschati\ValueObject\Types\Specials\Interfaces\SpecialInterface;
use Saschati\ValueObject\Behaviors\SpecialTypeBehavior;

class Money implements SpecialInterface
{


    /**
     * @param integer $value
     *
     * @return integer
     */
    public static function convertToPhpValue($value)
    {
        return $value * 100;
    }

    /**
     * @param integer $value
     *
     * @return integer
     */
    public static function convertToDatabaseValue($value)
    {
        return $value / 100;
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

/**
 * In database money equal 9.8
 */
$user = User::find()->one();

echo $user->money // 98
```
Road map
--------------
- In version 2.0.0, give the ability to combine strings from the database into one ValueObject.
- Add the ability to prescribe anonymous functions for input and output for SpecialType.
- Add semantics to Uuid.