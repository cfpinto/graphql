<?php


namespace GraphQL\Contracts\Entities;


use GraphQL\Contracts\Properties\IsStringableInterface;

interface ArgumentInterface extends IsStringableInterface
{
    public function getKey(): ?string;

    public function getValue();
}
