<?php


namespace GraphQL\Contracts\Entities;


use GraphQL\Contracts\Properties\IsStringableInterface;

interface AliasInterface extends IsStringableInterface
{
    public function getAlias(): ?string;

    public function setAlias(?string $alias): void;

    public function getKey(): ?string;

    public function setKey(?string $key): void;
}
