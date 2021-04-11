<?php


namespace GraphQL\Traits;


trait HasAttributesTrait
{
    protected array $attributes = [];

    final public function getAttributes(): array
    {
        return $this->attributes;
    }

    final public function hasAttributes(): bool
    {
        return count($this->attributes) > 0;
    }
}
