<?php
/**
 * PHP version 7.4
 *
 * @package Saschati\ValueObject\Types\Specials\Interfaces
 */

namespace Saschati\ValueObject\Types\Specials\Interfaces;

/**
 * Interface SpecialInterface
 */
interface SpecialInterface
{


    /**
     * @param $value
     *
     * @return mixed
     */
    public static function convertToPhpValue($value);


    /**
     * @param $value
     *
     * @return mixed
     */
    public static function convertToDatabaseValue($value);
}
