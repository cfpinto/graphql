<?php


namespace GraphQL\Contracts\Properties;


interface IsStringableInterface
{
    //TODO: Signature looks off with both toString
    public function __toString(): string;

    public function toString(): string;
}
