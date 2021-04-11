<?php


namespace GraphQL\Contracts\Parsers;


use GraphQL\Contracts\Properties\IsParsableInterface;

interface ParserInterface
{
    public function can(IsParsableInterface $parsable): bool;

    public function parse(IsParsableInterface $parsable, bool $singleLine = false): string;
}
