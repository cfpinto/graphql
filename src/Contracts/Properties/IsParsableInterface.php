<?php

namespace GraphQL\Contracts\Properties;

use GraphQL\Contracts\Parsers\ParserInterface;

interface IsParsableInterface
{
    public function parse(): string;

    public function singleLine(): string;

    public function setParsers(array $parsers = []): IsParsableInterface;

    /**
     * @return ParserInterface[]
     */
    public function getParsers(): array;
}
