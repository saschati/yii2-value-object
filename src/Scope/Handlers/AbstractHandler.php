<?php
/**
 * PHP version 8.1
 *
 * @package Saschati\ValueObject\Scope\Handlers
 */

namespace Saschati\ValueObject\Scope\Handlers;

use InvalidArgumentException;
use Saschati\ValueObject\Helpers\Property;
use Saschati\ValueObject\Scope\Bugs\Interfaces\BugInterface;
use Saschati\ValueObject\Scope\Handlers\Interfaces\AttributeInterface;
use Saschati\ValueObject\Scope\Handlers\Interfaces\HandlerInterface;
use Saschati\ValueObject\Scope\Handlers\Interfaces\ValueInterface;
use yii\db\ActiveRecordInterface;

use function array_key_exists;
use function array_key_last;
use function array_reduce;
use function array_shift;
use function explode;
use function is_array;
use function is_object;
use function ltrim;
use function property_exists;
use function str_starts_with;

/**
 * Class ConstructorHandler
 */
abstract class AbstractHandler implements HandlerInterface, AttributeInterface, ValueInterface
{
    /**
     * @var BugInterface
     */
    protected BugInterface $bug;


    /**
     * @param BugInterface $bug
     *
     * @return void
     */
    public function setBug(BugInterface $bug): void
    {
        $this->bug = $bug;
    }

    /**
     * ActiveRecord model with which the handler should interact.
     *
     * @return ActiveRecordInterface
     */
    abstract protected function getModel(): ActiveRecordInterface;

    /**
     * The property with which this handler interacts.
     *
     * @return string
     */
    abstract protected function getProperty(): string;

    /**
     * @param mixed $value
     *
     * @return void
     */
    public function setValue(mixed $value): void
    {
        $this->setAttribute($this->getModel(), $this->getProperty(), $value);
    }

    /**
     * @return mixed
     */
    public function getValue(): mixed
    {
        return $this->getAttribute($this->getModel(), $this->getProperty());
    }

    /**
     * @param ActiveRecordInterface $record
     * @param string                $name
     * @param mixed                 $value
     *
     * @return void
     */
    public function setAttribute(ActiveRecordInterface $record, string $name, mixed $value): void
    {
        $names = explode('.', $name);
        $name  = array_shift($names);

        if ($names === []) {
            if ($this->isVirtual($name) === true) {
                $this->bug->set($name, $value);

                return;
            }

            try {
                Property::setValue($record, $name, $value);
            } catch (InvalidArgumentException) {
                if ($this->isDataBase($name) === true) {
                    $name = ltrim($name, '@');
                }

                $record->setAttribute($name, $value);
            }

            return;
        }

        if ($this->isVirtual($name) === true) {
            $reference = $this->bug->get($name);

            $this->setDeepValue($names, $reference, $value);
            $this->bug->set($name, $reference);

            return;
        }

        try {
            $reference = Property::getValue($record, $name);

            $this->setDeepValue($names, $reference, $value);
            Property::setValue($record, $name, $reference);
        } catch (InvalidArgumentException) {
            if ($this->isDataBase($name) === true) {
                $name = ltrim($name, '@');
            }

            $reference = $record->getAttribute($name);

            $this->setDeepValue($names, $reference, $value);
            $record->setAttribute($name, $reference);
        }
    }

    /**
     * @param ActiveRecordInterface $record
     * @param string                $name
     *
     * @return mixed
     */
    public function getAttribute(ActiveRecordInterface $record, string $name): mixed
    {
        $names = explode('.', $name);
        $name  = array_shift($names);

        if ($names === []) {
            if ($this->isVirtual($name) === true) {
                return $this->bug->get($name);
            }

            try {
                return Property::getValue($record, $name);
            } catch (InvalidArgumentException) {
                if ($this->isDataBase($name) === true) {
                    $name = ltrim($name, '@');
                }

                return $record->getAttribute($name);
            }
        }

        if ($this->isVirtual($name) === true) {
            return $this->getDeepValue($names, $this->bug->get($name));
        }

        try {
            return $this->getDeepValue($names, Property::getValue($record, $name));
        } catch (InvalidArgumentException) {
            if ($this->isDataBase($name) === true) {
                $name = ltrim($name, '@');
            }

            return $this->getDeepValue($names, $record->getAttribute($name));
        }
    }

    /**
     * @param array $names
     * @param mixed $initial
     * @param mixed $value
     *
     * @return void
     */
    protected function setDeepValue(array $names, mixed &$initial, mixed $value): void
    {
        if ($names === []) {
            $initial = $value;

            return;
        }

        $lastIndex = array_key_last($names);

        $getter = function &($name) {
            if (property_exists($this, $name) === true) {
                return $this->{$name};
            }

            $value = null;

            return $value;
        };

        $setter = function ($name, $value) {
            if (property_exists($this, $name) === true) {
                $this->{$name} = $value;
            }
        };

        $reference = &$initial;
        foreach ($names as $index => $keyOrProperty) {
            if (is_array($reference) === true) {
                if ($lastIndex === $index) {
                    $reference[$keyOrProperty] = $value;

                    break;
                }

                $nextReference = &$reference[$keyOrProperty];
                if ($nextReference === null) {
                    break;
                }

                $reference = &$nextReference;

                continue;
            }

            if (is_object($reference) === true) {
                if ($lastIndex === $index) {
                    $setter->call($reference, $keyOrProperty, $value);

                    break;
                }

                /** @var callable $gt */
                $gt = $getter->bindTo($reference, $reference);

                $nextReference =& $gt($keyOrProperty);
                if ($nextReference === null) {
                    break;
                }

                $reference = &$nextReference;

                continue;
            }

            break;
        }//end foreach
    }

    /**
     * @param array $names
     * @param mixed $initial
     *
     * @return mixed
     */
    protected function getDeepValue(array $names, mixed $initial): mixed
    {
        $accumulator = static function (mixed $curr, string $name): mixed {
            if ($curr === null) {
                return null;
            }

            if (is_array($curr) === true) {
                return (array_key_exists($name, $curr) === true) ? $curr[$name] : null;
            }

            if (is_object($curr) === true) {
                try {
                    return Property::getValue($curr, $name);
                } catch (InvalidArgumentException) {
                    if ($curr instanceof ActiveRecordInterface) {
                        return $curr->getAttribute($name);
                    }
                }
            }

            return null;
        };

        return array_reduce($names, $accumulator, $initial);
    }

    /**
     * @param string $name
     *
     * @return boolean
     */
    protected function isVirtual(string $name): bool
    {
        return (str_starts_with($name, '#') === true);
    }

    /**
     * @param string $name
     *
     * @return boolean
     */
    protected function isDataBase(string $name): bool
    {
        return (str_starts_with($name, '@') === true);
    }
}
