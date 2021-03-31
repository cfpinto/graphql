<?php


namespace GraphQL\Contracts\Properties;


interface HasAttributesInterface
{
    public function getAttributes(): array;

    public function hasAttributes(): bool;
}
