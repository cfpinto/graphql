<?php


namespace GraphQL\Contracts\Properties;


use GraphQL\Collections\ArgumentsCollection;
use GraphQL\Contracts\Collections\StringableCollectionInterface;

interface HasArgumentsInterface
{
    public function hasArguments(): bool;

    public function getArguments(): ?StringableCollectionInterface;

    public function setArguments(ArgumentsCollection $arguments): HasArgumentsInterface;
}
