<?php
/**
 * PHP version 8.1
 *
 * @package Saschati\ValueObject\Scope\Handlers\Interfaces
 */

namespace Saschati\ValueObject\Scope\Handlers\Interfaces;

/**
 * Interface ValueInterface
 */
interface ValueInterface
{
    /**
     * @param mixed $value
     *
     * @return void
     */
    public function setValue(mixed $value): void;

    /**
     * @return mixed
     */
    public function getValue(): mixed;
}
