<?php


namespace GraphQL\Entities;


use GraphQL\Contracts\Entities\AliasInterface;
use GraphQL\Traits\IsStringableTrait;

class Alias implements AliasInterface
{
    use IsStringableTrait;

    protected ?string $alias;

    protected ?string $key;

    public function __construct(string $key, ?string $alias = null)
    {
        $this->setKey($key);
        $this->setAlias($alias);
    }

    public function toString(): string
    {
        return $this->getAlias() ?
            $this->getAlias() . ': ' . $this->getKey() :
            $this->getKey();
    }

    public function getAlias(): ?string
    {
        return $this->alias;
    }

    public function setAlias(?string $alias): void
    {
        $this->alias = $alias;
    }

    public function getKey(): ?string
    {
        return $this->key;
    }

    public function setKey(?string $key): void
    {
        $this->key = $key;
    }
}
