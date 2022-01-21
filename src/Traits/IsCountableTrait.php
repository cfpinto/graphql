<?php

namespace GraphQL\Traits;

trait IsCountableTrait
{
    protected array $elements = [];

    final public function count(): int
    {
        return count($this->elements);
    }
}
