<?php


namespace GraphQL\Contracts\Entities;


use GraphQL\Contracts\Properties\HasNameInterface;
use GraphQL\Contracts\Properties\IsStringableInterface;

interface VariableInterface extends IsStringableInterface, HasNameInterface
{
    public function getDefault(): string;

    public function setDefault(string $default): VariableInterface;

    public function getType(): string;

    public function setType(string $type): VariableInterface;
}
