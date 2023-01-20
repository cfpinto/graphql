<?php

namespace GraphQL\Traits;

trait HasArrayAccessTrait
{
    protected array $elements = [];

    final public function offsetSet($offset, $value): void
    {
        if (empty($offset) && $offset !== 0) {
            $this->elements[] = $value;
            return;
        }

        $this->elements[$offset] = $value;

    }

    final public function offsetUnset($offset): void
    {
        unset($this->elements[$offset]);
    }

    final public function offsetExists($offset): bool
    {
        return isset($this->elements[$offset]);
    }

    final public function offsetGet($offset): mixed
    {
        return $this->elements[$offset];
    }
}
