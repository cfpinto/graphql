<?php


namespace GraphQL\Abstracts;


use GraphQL\Collections\ArgumentsCollection;
use GraphQL\Contracts\Entities\NodeInterface;
use GraphQL\Exceptions\InvalidArgumentTypeException;
use GraphQL\Traits\HasArguments;
use GraphQL\Traits\HasAttributesTrait;
use GraphQL\Traits\HasFragmentsTrait;
use GraphQL\Traits\HasInlineFragmentsTrait;
use GraphQL\Traits\HasNameTrait;
use GraphQL\Traits\HasNodesTrait;
use GraphQL\Traits\HasParentTrait;
use GraphQL\Traits\IsParsableTrait;
use GraphQL\Traits\IsStringableTrait;

/**
 * Class NodeAbstract
 *
 * @method NodeInterface use (...$arguments)
 *
 * @package GraphQL\Abstracts
 */
abstract class NodeAbstract implements NodeInterface
{
    use HasAttributesTrait;
    use HasArguments;
    use HasFragmentsTrait;
    use HasInlineFragmentsTrait;
    use HasNameTrait;
    use HasNodesTrait;
    use HasParentTrait;
    use IsStringableTrait;
    use IsParsableTrait;

    public function __construct(string $name, array $arguments = [], array $presenters = [])
    {
        $this->setName($name);
        $this->setParsers($presenters);
        $this->setArguments(new ArgumentsCollection($arguments));
    }

    final public function __get($name): NodeInterface
    {
        return $this->generate($name);
    }

    /**
     * @param $name
     * @param $arguments
     *
     * @return NodeInterface
     * @throws InvalidArgumentTypeException
     */
    final public function __call($name, $arguments): NodeInterface
    {
        if ($name === 'use') {
            return $this->get($arguments);
        }

        if (!isset($arguments[0]) || !is_array($arguments[0])) {
            throw new InvalidArgumentTypeException(gettype($arguments[0]));
        }

        return $this->generate($name, $arguments[0] ?? []);
    }

    final public function __set(string $name, NodeInterface $value): NodeInterface
    {
        return $this->addChild($name, $value);
    }

    final public function toString(): string
    {
        $string = '';

        foreach ($this->parsers as $parser) {
            if ($parser->can($this)) {
                $string .= $parser->parse($this, $this->singleLine);
            }
        }

        return $string;
    }

    /**
     * @deprecated
     * @see NodeAbstract::parse()
     */
    final public function query(): void
    {
        echo $this->toString();
    }

    final public function clear(): self
    {
        $this->attributes = [];
        $this->children = [];

        return $this;
    }

    abstract protected function get(array $arguments): NodeInterface;

    abstract protected function generate(string $name, array $arguments = []): NodeInterface;
}
