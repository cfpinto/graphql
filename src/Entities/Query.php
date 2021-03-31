<?php


namespace GraphQL\Entities;


use GraphQL\Contracts\Entities\RootNodeInterface;
use GraphQL\Contracts\Entities\VariableInterface;
use GraphQL\Contracts\Properties\HasVariablesInterface;
use GraphQL\Parsers\QueryParser;
use GraphQL\Traits\HasFragmentsTrait;

class Query extends Node implements RootNodeInterface
{
    protected array $variables = [];

    public function __construct(string $name, array $arguments = [])
    {
        parent::__construct($name, $arguments);

        $this->setParsers([new QueryParser()]);
    }

    final public function removeVariable(VariableInterface $variable): HasVariablesInterface
    {
        // TODO: Implement removeVariable() method.
        return $this;
    }

    final public function getVariables(): array
    {
        return $this->variables;
    }

    final public function hasVariables(): bool
    {
        return count($this->variables) > 0;
    }

    final public function addVariable(VariableInterface $variable): self
    {
        $this->variables[] = $variable;

        return $this;
    }
}
