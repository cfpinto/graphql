<?php


namespace GraphQL\Abstracts;


use GraphQL\Contracts\Collections\StringableCollectionInterface;
use GraphQL\Traits\HasArrayAccessTrait;
use GraphQL\Traits\IsCountableTrait;
use GraphQL\Traits\IsIteratorTrait;
use GraphQL\Traits\IsStringableTrait;

abstract class CollectionAbstract implements StringableCollectionInterface
{
    use IsStringableTrait, IsCountableTrait, HasArrayAccessTrait, IsIteratorTrait;

    protected array $elements = [];

    final public function clear(): void
    {
        $this->elements = [];
    }

    abstract public function toString(): string;
}
