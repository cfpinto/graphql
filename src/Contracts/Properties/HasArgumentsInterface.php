<?php


namespace GraphQL\Contracts\Properties;

use GraphQL\Contracts\Collections\StringableCollectionInterface;

interface HasArgumentsInterface
{
    public function hasArguments(): bool;

    public function getArguments(): ?StringableCollectionInterface;

    public function setArguments(StringableCollectionInterface $arguments): HasArgumentsInterface;

    public function rootVariables(): void;
}
