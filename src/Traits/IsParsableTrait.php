<?php

namespace GraphQL\Traits;

use GraphQL\Contracts\Parsers\ParserInterface;
use GraphQL\Contracts\Properties\IsParsableInterface;

trait IsParsableTrait
{
    protected bool $singleLine = false;

    /**
     * @var ParserInterface[]
     */
    protected array $parsers = [];

    final public function setParsers(array $parsers = []): IsParsableInterface
    {
        $this->parsers = $parsers;

        return $this;
    }

    final public function getParsers(): array
    {
        return $this->parsers;
    }

    final public function parse(): string
    {
        $this->singleLine = false;

        return $this->toString();
    }

    final public function singleLine(): string
    {
        $this->singleLine = true;

        return $this->toString();
    }
}
