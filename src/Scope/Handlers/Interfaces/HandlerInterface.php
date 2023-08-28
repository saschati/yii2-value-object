<?php
/**
 * PHP version 8.1
 *
 * @package Saschati\ValueObject\Scope\Handlers\Interfaces
 */

namespace Saschati\ValueObject\Scope\Handlers\Interfaces;

use Saschati\ValueObject\Scope\Bugs\Interfaces\BugInterface;

/**
 * Interface HandlerInterface
 *
 * An interface for implementing handlers that must process fields in behavior.
 */
interface HandlerInterface
{
    /**
     * @param BugInterface $bug
     *
     * @return void
     */
    public function setBug(BugInterface $bug): void;

    /**
     * The method that will be called after data extraction and that should process the specified field.
     *
     * @return void
     */
    public function cast(): void;

    /**
     * A method that will be called before saving the data and that must process the specified field,
     * converting it to a value for the database.
     *
     * @return void
     */
    public function normalize(): void;
}
