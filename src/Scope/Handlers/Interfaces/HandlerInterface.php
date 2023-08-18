<?php
/**
 * PHP version 8.1
 *
 * @package Saschati\ValueObject\Scope\Handlers\Interfaces
 */

namespace Saschati\ValueObject\Scope\Handlers\Interfaces;

/**
 * Interface HandlerInterface
 */
interface HandlerInterface
{
    /**
     * @return void
     */
    public function cast(): void;

    /**
     * @return void
     */
    public function normalize(): void;
}