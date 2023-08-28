<?php
/**
 * PHP version 8.1
 *
 * @package Saschati\ValueObject\Scope\Bugs\Interfaces
 */

namespace Saschati\ValueObject\Scope\Bugs;

use Saschati\ValueObject\Scope\Bugs\Interfaces\BugInterface;

/**
 * Class VirtualPropertyBug
 *
 * A bag of virtual properties, casting values in behavior that are denoted by "#".
 * [
 *     '#virtualPropertyOrBuild' => 'databaseAttribute'
 * ]
 */
class VirtualPropertyBug implements BugInterface
{
    /**
     * @param array $bug
     */
    public function __construct(
        protected array $bug = [],
    ) {
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function get(string $name): mixed
    {
        return ($this->bug[$name] ?? null);
    }

    /**
     * @param string $name
     * @param mixed  $value
     *
     * @return void
     */
    public function set(string $name, mixed $value): void
    {
        $this->bug[$name] = $value;
    }
}
