[Back](../README.md)

Mapped attribute, using [MapperHandler](../src/Scope/Handlers/MapperHandler.php)
================================================
This handler provides the ability to assign model attributes to class properties, and reverse them on save.

Usage
-----
```php
use Saschati\ValueObject\Behaviors\ORMBehavior;
use Saschati\ValueObject\Helpers\TypeScope;
use Saschati\ValueObject\Scope\Handlers\MapperHandler;
...

class User extends ActiveRecord
{
    /**
     * @var string 
     */
    private string $id;
    
    /**
     * @var string 
     */
    private string $createdAt;
    
    /**
     * @var string 
     */
    private string $status;

    ...
    public function behaviors(): array
    {
        return [
            'vo' => [
                'class' => ORMBehavior::class,
                'attributes' => [
                    'mapper' => [
                        'scope' => TypeScope::MAPPER, // MapperHandler::class
                        'map' => [
                            'id' => '@id',
                            'createdAt' => 'created_at',
                            'status' => '@status',
                        ]
                    ],
                ],
            ],
        ];
    }
    ...
}
```
This handler will be applied if "scope" MAPPER is specified in the array,
the library itself keeps track of property mapping.
Please note that if the attribute and the property have the same name, then the prefix "@" should be added to the attribute.

List of MapperHandler properties
--------------
| name                                | type                                                                                                                     | description                                                                                                 |
|-------------------------------------|--------------------------------------------------------------------------------------------------------------------------|-------------------------------------------------------------------------------------------------------------|
| map<span style="color:red">*</span> | array of key property and value [@attribute, property, #virtual](../README.md#main-property), `[property => @attribute]` | A property that indicates that a property from the model class corresponds to an attribute from the model.  |