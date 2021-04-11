<?php


namespace GraphQL\Traits;


use GraphQL\Contracts\Entities\VariableInterface;
use GraphQL\Contracts\Properties\HasVariablesInterface;

trait HasVariablesTrait
{
    protected array $variables = [];

    final public function removeVariable(VariableInterface $variable): HasVariablesInterface
    {
        $match = array_filter(
            $this->variables,
            fn(VariableInterface $item) => $item->toString() === $variable->toString(),
        );

        if (count($match) > 0) {
            unset($this->variables[key($match)]);
        }

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

    final public function addVariable(VariableInterface $variable): HasVariablesInterface
    {
        /** @var VariableInterface $var */
        foreach ($this->variables as $var) {
            if ($variable->getName() === $var->getName()) {
                return $this;
            }
        }

        $this->variables[] = $variable;

        return $this;
    }
}
