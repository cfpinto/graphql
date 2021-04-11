<?php


namespace GraphQL\Entities;


use GraphQL\Contracts\Entities\VariableInterface;
use GraphQL\Contracts\Parsers\ParserInterface;
use GraphQL\Traits\IsStringableTrait;

class Variable implements VariableInterface
{
    use IsStringableTrait;

    protected string $name;

    protected string $type;

    protected ?string $default;

    public function __construct(string $name, string $type, string $default = '')
    {
        $this->setName($name);
        $this->setType($type);
        $this->setDefault($default);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDefault(): string
    {
        return $this->default;
    }

    public function setDefault(string $default): self
    {
        $this->default = $default;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function toString(): string
    {
        return '$' . $this->getName() . ': ' .
            ($this->getType()) .
            ($this->getDefault() ?
                ' = ' .
                ($this->getType() === 'String' ? json_encode($this->getDefault()) : $this->getDefault())
                : '');
    }
}
