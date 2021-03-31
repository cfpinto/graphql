<?php


namespace GraphQL\Contracts\Properties;


interface HasNameInterface
{
    public function getName(): string;

    public function setName(string $name): HasNameInterface;
}
