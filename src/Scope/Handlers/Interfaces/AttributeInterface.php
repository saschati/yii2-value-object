<?php
/**
 * PHP version 8.1
 *
 * @package Saschati\ValueObject\Scope\Handlers\Interfaces
 */

namespace Saschati\ValueObject\Scope\Handlers\Interfaces;

use yii\db\ActiveRecordInterface;

/**
 * Interface AttributeInterface
 *
 * The interface for manipulating the ActiveRecord object and its attributes.
 */
interface AttributeInterface
{
    /**
     * Add values to attributes or to the corresponding bag.
     *
     * @param ActiveRecordInterface $record
     * @param string                $name
     * @param mixed                 $value
     *
     * @return void
     */
    public function setAttribute(ActiveRecordInterface $record, string $name, mixed $value): void;

    /**
     * Get the value from the attributes or from the corresponding bag.
     *
     * @param ActiveRecordInterface $record
     * @param string                $name
     *
     * @return mixed
     */
    public function getAttribute(ActiveRecordInterface $record, string $name): mixed;
}
