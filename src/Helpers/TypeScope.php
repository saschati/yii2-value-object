<?php
/**
 * PHP version 8.1
 *
 * @package Saschati\ValueObject\Helpers
 */

namespace Saschati\ValueObject\Helpers;

use Saschati\ValueObject\Scope\Handlers\ConstructorHandler;
use Saschati\ValueObject\Scope\Handlers\EachHandler;
use Saschati\ValueObject\Scope\Handlers\EmbeddedHandler;
use Saschati\ValueObject\Scope\Handlers\FlatTypeHandler;
use Saschati\ValueObject\Scope\Handlers\MapperHandler;
use Saschati\ValueObject\Scope\Handlers\ValueObjectHandler;
use Saschati\ValueObject\Scope\Handlers\YiiCreateHandler;

/**
 * Class TypeScope
 */
class TypeScope
{
    /**
     * @see ValueObjectHandler
     */
    public const VALUE_OBJECT_TYPE = 'value_object_type';
    /**
     * @see FlatTypeHandler
     */
    public const FLAT_TYPE = 'flat_type';
    /**
     * @see EmbeddedHandler
     */
    public const EMBEDDED = 'embedded';
    /**
     * @see ConstructorHandler
     */
    public const CONSTRUCTOR = 'constructor';
    /**
     * @see YiiCreateHandler
     */
    public const YII_CREATE = 'yii_create';
    /**
     * @see MapperHandler
     */
    public const MAPPER = 'mapper';
    /**
     * @see EachHandler
     */
    public const EACH = 'each';
}
