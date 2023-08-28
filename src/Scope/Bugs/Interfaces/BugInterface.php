<?php
/**
 * PHP version 8.1
 *
 * @package Saschati\ValueObject\Scope\Bugs\Interfaces
 */

namespace Saschati\ValueObject\Scope\Bugs\Interfaces;

/**
 * Interface BugInterface
 */
interface BugInterface
{
    /**
     * @param string $name
     *
     * @return mixed
     */
    public function get(string $name): mixed;

    /**
     * @param string $name
     * @param mixed  $value
     *
     * @return void
     */
    public function set(string $name, mixed $value): void;
}
