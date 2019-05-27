<?php

namespace Nbj;

use Countable;
use ArrayAccess;
use RuntimeException;
use ReflectionFunction;
use ReflectionException;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;

class Collection implements ArrayAccess, Countable
{
    /**
     * Holds all the items in the collection
     *
     * @var array $items
     */
    protected $items = [];

    /**
     * Named construct for creating a new collection
     *
     * @param array $items
     *
     * @return static
     */
    public static function make($items = [])
    {
        return new static($items);
    }

    /**
     * Collection constructor.
     *
     * @param array $items
     */
    public function __construct($items = [])
    {
        if (!is_array($items)) {
            $items = [$items];
        }

        $this->items = $items;
    }

    /**
     * Pushes an item to the collection
     *
     * @param mixed $item
     *
     * @return $this
     */
    public function push($item)
    {
        array_push($this->items, $item);

        return $this;
    }

    /**
     * Pops off the last item in the collection
     *
     * @return mixed
     */
    public function pop()
    {
        return array_pop($this->items);
    }

    /**
     * Shifts off the first item in the collection
     *
     * @return mixed
     */
    public function shift()
    {
        return array_shift($this->items);
    }

    /**
     * Adds an item to the collection
     * syntactic sugar for pushing to
     * the collection
     *
     * @param mixed $item
     *
     * @return $this
     */
    public function add($item)
    {
        return $this->push($item);
    }

    /**
     * Checks if the collection is empty
     *
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->items);
    }

    /**
     * Syntactic sugar for checking if the
     * collection is not empty
     *
     * @return bool
     */
    public function isNotEmpty()
    {
        return !$this->isEmpty();
    }

    /**
     * Returns the first item of the collection
     *
     * @return mixed
     */
    public function first()
    {
        return reset($this->items);
    }

    /**
     * Returns the last item of the collection
     *
     * @return mixed
     */
    public function last()
    {
        return end($this->items);
    }

    /**
     * Iterates over each item in the collection
     *
     * @param callable $callback
     *
     * @return $this
     */
    public function each(callable $callback)
    {
        array_walk($this->items, $callback);

        return $this;
    }

    /**
     * Maps over each item in the collection
     *
     * @param callable $callback
     *
     * @return Collection
     */
    public function map(callable $callback)
    {
        return new Collection(array_map($callback, $this->items));
    }

    /**
     * Applies a filter to the collection
     *
     * @param callable $callback
     *
     * @return Collection
     *
     * @throws ReflectionException
     * @throws RuntimeException
     */
    public function filter(callable $callback)
    {
        $reflectionMethod = new ReflectionFunction($callback);
        $numberOfParameters = $reflectionMethod->getNumberOfParameters();

        if ($numberOfParameters == 1) {
            return new Collection(array_filter($this->items, $callback));
        }

        if ($numberOfParameters == 2) {
            return new Collection(array_filter($this->items, $callback, ARRAY_FILTER_USE_BOTH));
        }

        throw new RuntimeException('Too many parameters for filter() callback');
    }

    /**
     * Rejects items in an array
     *
     * @param callable $callback
     *
     * @return Collection
     *
     * @throws ReflectionException
     * @throws RuntimeException
     */
    public function reject(callable $callback)
    {
        $reflectionMethod = new ReflectionFunction($callback);
        $numberOfParameters = $reflectionMethod->getNumberOfParameters();

        if ($numberOfParameters == 1) {
            return $this->filter(function ($item) use ($callback) {
                return !$callback($item);
            });
        }

        if ($numberOfParameters == 2) {
            return $this->filter(function ($value, $key) use ($callback) {
                return !$callback($value, $key);
            });
        }

        throw new RuntimeException('Too many parameters for reject() callback');
    }

    /**
     * Performs a truth test on each item in the collection
     *
     * @param callable $callback
     *
     * @return bool
     *
     * @throws ReflectionException
     */
    public function every(callable $callback)
    {
        $filteredCollection = $this->filter($callback);

        return $filteredCollection->count() == $this->count();
    }

    /**
     * It can reduce a collection to a single value
     *
     * @param callable $callback
     * @param mixed $initial
     *
     * @return mixed
     */
    public function reduce(callable $callback, $initial)
    {
        return array_reduce($this->items, $callback, $initial);
    }

    /**
     * Summarizes the collection
     *
     * @param mixed $field
     *
     * @return mixed
     */
    public function sum($field = null)
    {
        // Assume the collection is index based and contains only numbers
        if (is_null($field)) {
            return array_sum($this->items);
        }

        // Assume the collection contains arrays or objects
        return $this->reduce(function ($carry, $item) use ($field) {
            if (is_array($item)) {
                $carry += $item[$field];
            }

            if (is_object($item)) {
                $carry += $item->$field;
            }

            return $carry;
        }, 0);
    }

    /**
     * Flattens a Collection recursively
     *
     * @return Collection
     */
    public function flatten()
    {
        $flattenedItems = new Collection;
        $items = new RecursiveIteratorIterator(new RecursiveArrayIterator($this->items));

        foreach ($items as $item) {
            $flattenedItems->push($item);
        }

        return $flattenedItems;
    }

    /**
     * Implodes all non objecty items of the collection
     *
     * @param $glue
     *
     * @return string
     *
     * @throws ReflectionException
     */
    public function implode($glue = "")
    {
        $items = $this->filter(function ($item) {
            return is_numeric($item) || is_string($item);
        })->toArray();

        return implode($glue, $items);
    }

    /**
     * Converts the collection to an array
     * by returning the underlying array
     *
     * @return array
     */
    public function toArray()
    {
        return $this->items;
    }

    /**
     * Converts the collection to json
     *
     * @param int $options
     *
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->items, $options);
    }

    /**
     * Whether a offset exists
     *
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     *
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     *
     * @return bool true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     *
     * @since 5.0.0
     */
    public function offsetExists($offset)
    {
        return isset($this->items[$offset]);
    }

    /**
     * Offset to retrieve
     *
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     *
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     *
     * @return mixed Can return all value types.
     *
     * @since 5.0.0
     */
    public function offsetGet($offset)
    {
        return $this->items[$offset];
    }

    /**
     * Offset to set
     *
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     *
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     *
     * @return void
     *
     * @since 5.0.0
     */
    public function offsetSet($offset, $value)
    {
        $this->items[$offset] = $value;
    }

    /**
     * Offset to unset
     *
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     *
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     *
     * @return void
     *
     * @since 5.0.0
     */
    public function offsetUnset($offset)
    {
        unset($this->items[$offset]);
    }

    /**
     * Count elements of an object
     *
     * @link http://php.net/manual/en/countable.count.php
     *
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     *
     * @since 5.1.0
     */
    public function count()
    {
        return count($this->items);
    }
}
