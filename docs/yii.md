[Back](../README.md)

CreateCreate instance via Yii::create, using [YiiCreateHandler](../src/Scope/Handlers/YiiCreateHandler.php)
================================================
This handler provides an opportunity to create a built-in type by using the [Yii::createObject](https://www.yiiframework.com/doc/api/2.0/yii-baseyii#createObject()-detail) and mapping model attributes to it.

Usage
-----
```php
use Saschati\ValueObject\Behaviors\ORMBehavior;
use Saschati\ValueObject\Helpers\TypeScope;
use Saschati\ValueObject\Scope\Handlers\YiiCreateHandler;
...

class User extends ActiveRecord
{    
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
                    'verifyEmail' => [
                        'scope' => TypeScope::YII_CREATE, // YiiCreateHandler::class
                        'type' => Token::class,
                        'params' => [
                            'verify_email_token',
                            'verify_email_life_at',
                        ],
                        'resolver' => static function (?Token $type, User $user, YiiCreateHandler $handler) {
                             $model->verify_email_token = $type?->getToken();
                             $model->verify_email_life_at = $type?->getLifeAt();
                         },
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
    ...
    public function __construct(
        private string $token,
        private DateTimeInterface $lifeAt,
        private SettingRepository $repository,
    ) {
    }
    
    ...
    /**
     * @return boolean
     *
     * @throws Exception
     */
    public function isValidLifeAt(): bool
    {
        $lifeAt = new DateTimeImmutable($this->repository->getExpireToken());

        return $this->lifeAt->getTimestamp() >= $lifeAt->getTimestamp();
    }
    ...
    
    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }
    
    /**
     * @return DateTimeInterface
     */
    public function getLifeAt(): DateTimeInterface
    {
        return $this->lifeAt;
    }
}

```

This handler will be applied if "scope" YII_CREATE is specified in the array,
the library itself NOT keeps track of property mapping.
[EmbeddedHandler](embedded.md) is much more convenient in this regard, so pay attention to it,
but if you need additional capabilities provided by Yii::createObject, it will not be a bad option.

List of YiiCreateHandler properties
--------------
| name                                     | type                                                                                                                                                                                                                    | description                                                                                                                                                                                          |
|------------------------------------------|-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| type<span style="color:red">*</span>     | [::class](https://www.php.net/manual/en/language.oop5.basic.php#language.oop5.basic.class.class)                                                                                                                        | Can be any class with properties of various access modifiers                                                                                                                                         |
| params<span style="color:red">*</span>   | array of value [@attribute, property, #virtual](../README.md#main-property), `[@attribute1, @attribute2]`, or any scalar type                                                                                           | A property that specifies which attributes should be passed in built-in object class.                                                                                                                |
| resolver<span style="color:red">*</span> | static function (?object $type, [ActiveRecordInterface](https://www.yiiframework.com/doc/api/2.0/yii-db-activerecordinterface) $model, [YiiCreateHandler](../src/Scope/Handlers/ConstructorHandler.php) $handler): void | Mandatory handler that will resolve how the type corresponds to the model when saved to the database.                                                                                                |
| nullIf                                   | static function ([ActiveRecordInterface](https://www.yiiframework.com/doc/api/2.0/yii-db-activerecordinterface) $model, [YiiCreateHandler](../src/Scope/Handlers/ConstructorHandler.php) $handler): bool                | The property that receives the callback indicates whether the key of the array is a property with a value of this type, or whether it should be null at the stage of data mapping from the database. |

Road map
--------------
- Add the ability to track the properties/methods of getters that should be supplied to replace the resolver.