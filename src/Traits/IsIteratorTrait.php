<?php

namespace GraphQL\Traits;

trait IsIteratorTrait
{
    protected array $elements = [];

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
}
