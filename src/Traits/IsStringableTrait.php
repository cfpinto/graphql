<?php


namespace GraphQL\Traits;


trait IsStringableTrait
{
    final public function __toString(): string
    {
        return $this->toString();
    }
}
