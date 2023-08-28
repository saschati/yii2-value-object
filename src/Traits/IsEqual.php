<?php
/**
 * PHP version 8.1
 *
 * @package App\Utils\ValueObject
 */

namespace Saschati\ValueObject\Traits;

use Saschati\ValueObject\Types\ValueObjects\Abstracts\NativeType;

/**
 * Class IsEqual
 *
 * Comparison of two Native types.
 *
 * @see NativeType::getValue()
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
