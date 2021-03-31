<?php


namespace GraphQL\Contracts\Properties;


use GraphQL\Contracts\Entities\VariableInterface;

interface HasVariablesInterface
{
    public function removeVariable(VariableInterface $variable): HasVariablesInterface;

    /**
     * @return VariableInterface[]
     */
    public function getVariables(): array;

    public function hasVariables(): bool;

    public function addVariable(VariableInterface $variable): HasVariablesInterface;
}
