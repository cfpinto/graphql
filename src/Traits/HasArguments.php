<?php


namespace GraphQL\Traits;


use GraphQL\Collections\ArgumentsCollection;
use GraphQL\Contracts\Properties\HasArgumentsInterface;

trait HasArguments
{
    protected ?ArgumentsCollection $arguments = null;

    final public function hasArguments(): bool
    {
        return $this->arguments->count() > 0;
    }

    final public function getArguments(): ?ArgumentsCollection
    {
        return $this->arguments;
    }

    final public function setArguments(ArgumentsCollection $arguments): HasArgumentsInterface
    {
        $this->arguments = $arguments;

        return $this;
    }
}
