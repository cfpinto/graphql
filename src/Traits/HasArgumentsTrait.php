<?php


namespace GraphQL\Traits;


use GraphQL\Collections\ArgumentsCollection;
use GraphQL\Contracts\Collections\StringableCollectionInterface;
use GraphQL\Contracts\Entities\VariableInterface;
use GraphQL\Contracts\Properties\HasArgumentsInterface;

trait HasArgumentsTrait
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

    final public function setArguments(StringableCollectionInterface $arguments): HasArgumentsInterface
    {
        $this->arguments = $arguments;
        $this->rootVariables();

        return $this;
    }

    final public function rootVariables(): void
    {
        foreach ($this->getArguments() as $argument) {
            if ($argument instanceof VariableInterface && $this->root()) {
                $this->root()->addVariable($argument);
            }
        }
    }
}
