<?php


namespace GraphQL\Contracts\Entities;


use GraphQL\Contracts\Properties\HasAttributesInterface;
use GraphQL\Contracts\Properties\HasNameInterface;
use GraphQL\Contracts\Properties\HasParentInterface;
use GraphQL\Contracts\Properties\IsStringableInterface;

interface FragmentInterface extends IsStringableInterface, HasParentInterface, HasNameInterface, HasAttributesInterface
{
    public function getOnType(): string;

    public function setOnType(string $onType): FragmentInterface;

    public function inline(): string;
}
