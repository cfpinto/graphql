<?php


namespace GraphQL\Contracts\Properties;


interface IsStringableInterface
{
    public function __toString(): string;

    public function toString(): string;
}
