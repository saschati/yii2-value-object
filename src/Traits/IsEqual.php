<?php
/**
 * PHP version 8.1
 *
 * @package App\Utils\ValueObject
 */

namespace Saschati\ValueObject\Traits;

/**
 * Class IsEqual
 */
trait IsEqual
{
    /**
     * @param self $other
     *
     * @return boolean
     */
    public function isEqual(self $other): bool
    {
        return $this->getValue() === $other->getValue();
    }
}
