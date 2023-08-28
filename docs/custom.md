[Back](../README.md)

Custom handler, using [AbstractHandler](../src/Scope/Handlers/AbstractHandler.php) or [HandlerInterface](../src/Scope/Handlers/Interfaces/HandlerInterface.php)
---
You can also implement your own handler using the AbstractHandler class or HandlerInterface and use it as follows:
```php
use Saschati\ValueObject\Behaviors\ORMBehavior;
use Saschati\ValueObject\Scope\Handlers\AbstractHandler;
use yii\db\ActiveRecordInterface;

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
                    'property' => [
                        'scope' => CustomHandler::class,
                        'key1' => 'value1',
                        'key2' => 'value2',
                    ],
                ],
            ],
        ];
    }
    ...
}

class CustomHandler extends AbstractHandler
{
    /**
     * @param ActiveRecordInterface $model A model that uses a handler
     * @param string $attribute The current attribute
     * @param array $type The entire array is passed through behavior
     */
    public function __construct(
        private readonly ActiveRecordInterface $model,
        private readonly string $attribute,
        private readonly array $type
    ) {
    }
    
    /**
     * The method that will be called after data extraction and
     * that should process the specified field.
     *
     * @return void
     */
    public function cast() : void
    {
        // TODO: Implement cast() method.
    }
    
    /**
     * A method that will be called before saving the data and that must process
     * the specified field, converting it to a value for the database.
     *
     * @return void
     */
    public function normalize() : void
    {
        // TODO: Implement normalize() method.
    }
    
    /**
     * @return ActiveRecordInterface
     */
    protected function getModel(): ActiveRecordInterface
    {
        return $this->model;
    }

    /**
     * @return string
     */
    protected function getProperty(): string
    {
        return $this->attribute;
    }
}
```
The abstract handler provides a useful API that you may need when using its handlers,
namely these method.
This class is able to work through its methods with virtual properties,
attributes and properties of the class, as well as with nested properties.

API Methods
---
| methods                                                                                         | description                                                                                                                                |
|-------------------------------------------------------------------------------------------------|--------------------------------------------------------------------------------------------------------------------------------------------|
| `public function setAttribute(ActiveRecordInterface $record, string $name, mixed $value): void` | Add values to attributes or to the corresponding bag. Works with nested properties, virtual properties, or database attributes.            |
| `public function getAttribute(ActiveRecordInterface $record, string $name): mixed;`             | Get the value from the attributes or from the corresponding bag. Works with nested properties, virtual properties, or database attributes. |
| `public function setValue(mixed $value): void;`                                                 | A short version of the `setAttribute` method.                                                                                              |
| `public function getValue(): mixed;`                                                            | A short version of the `getAttribute` method.                                                                                              |
