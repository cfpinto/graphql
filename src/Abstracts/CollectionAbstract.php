<?php


namespace GraphQL\Abstracts;


use GraphQL\Contracts\Collections\StringableCollectionInterface;
use GraphQL\Traits\IsStringableTrait;

abstract class CollectionAbstract implements StringableCollectionInterface
{
    use IsStringableTrait;

    protected array $elements = [];

    final public function clear(): void
    {
        $this->elements = [];
    }

    final public function current()
    {
        return current($this->elements);
    }

    final public function next()
    {
        next($this->elements);
    }

    final public function key()
    {
        return key($this->elements);
    }

    final public function valid(): bool
    {
        return (bool)current($this->elements);
    }

    final public function rewind()
    {
        reset($this->elements);
    }

    final public function offsetExists($offset): bool
    {
        return isset($this->elements[$offset]);
    }

    final public function offsetGet($offset)
    {
        return $this->elements[$offset];
    }

    final public function offsetSet($offset, $value)
    {
        if (empty($offset) && $offset !== 0) {
            $this->elements[] = $value;
        } else {
            $this->elements[$offset] = $value;
        }
    }

    final public function offsetUnset($offset)
    {
        unset($this->elements[$offset]);
    }

    final public function count(): int
    {
        return count($this->elements);
    }

    abstract public function toString(): string;
}
