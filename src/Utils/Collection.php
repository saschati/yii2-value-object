<?php
/**
 * PHP version 8.1
 *
 * @package App\Utils
 */

namespace Saschati\ValueObject\Utils;

use Ramsey\Collection\Collection as BaseCollection;
use Ramsey\Collection\Exception\InvalidArgumentException;

use function sprintf;

/**
 * Class Collection
 *
 * @template T
 * @extends  BaseCollection<T>
 */
class Collection extends BaseCollection
{
    /**
     * A method that allows you to add a value to the beginning of a collection
     *
     * @param T|mixed $value The element to unshift to the collection.
     *
     * @return boolean `true` if this collection changed as a result of the call.
     *
     * @throws InvalidArgumentException
     */
    public function unshift(mixed $value): bool
    {
        if ($this->checkType($this->getType(), $value) === false) {
            throw new InvalidArgumentException(
                sprintf('Value must be of type %s; value is %s', $this->getType(), $this->toolValueToString($value))
            );
        }

        $this->data = [
            $value,
            ...$this->data,
        ];

        return true;
    }

    /**
     * Filter the elements of the collection, and returns the first one found, otherwise null.
     *
     * See the {@link http://php.net/manual/en/function.array-filter.php PHP array_filter() documentation}
     * for examples of how the `$callback` parameter works.
     *
     * @param callable(T):bool $callback A callable to use for filtering elements.
     *
     * @return T|null
     */
    public function find(callable $callback): mixed
    {
        $collection = $this->filter($callback);

        if ($collection->isEmpty() === true) {
            return null;
        }

        return $collection->first();
    }
}
