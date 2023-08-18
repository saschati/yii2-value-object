<?php
/**
 * PHP version 8.1
 *
 * @package Saschati\ValueObject\Helpers
 */

namespace Saschati\ValueObject\Helpers;

/**
 * Class TypeScope
 */
class TypeScope
{
    public const VALUE_OBJECT_TYPE = 'value_object_type';
    public const FLAT_TYPE         = 'flat_type';
    public const EMBEDDED          = 'embedded';
    public const CONSTRUCTOR       = 'constructor';
    public const YII_CREATE        = 'yii_create';
    public const MAPPER            = 'mapper';
    public const EACH              = 'each';
}
