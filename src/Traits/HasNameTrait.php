<?php


namespace GraphQL\Traits;


use GraphQL\Contracts\Properties\HasNameInterface;

trait HasNameTrait
{
    protected ?string $name;

    final public function setName(string $name): HasNameInterface
    {
        $this->name = $name;

        return $this;
    }

    final public function getName(): string
    {
        return $this->name;
    }
}
