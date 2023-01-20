<?php

namespace GraphQL\Traits;

trait IsIteratorTrait
{
    protected array $elements = [];

    final public function current(): mixed
    {
        return current($this->elements);
    }

    final public function next(): void
    {
        next($this->elements);
    }

    final public function key(): int|string|null
    {
        return key($this->elements);
    }

    final public function valid(): bool
    {
        return (bool)current($this->elements);
    }

    final public function rewind(): void
    {
        reset($this->elements);
    }
}
