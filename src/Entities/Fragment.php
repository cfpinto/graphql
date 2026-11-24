<?php


namespace GraphQL\Entities;

use GraphQL\Contracts\Entities\FragmentInterface;
use GraphQL\Contracts\Entities\InlineFragmentInterface;
use GraphQL\Contracts\Entities\NodeInterface;
use GraphQL\Contracts\Entities\VariableInterface;
use GraphQL\Contracts\Properties\IsStringableInterface;
use GraphQL\Exceptions\InvalidArgumentTypeException;
use GraphQL\Traits\HasAttributesTrait;
use GraphQL\Traits\HasNameTrait;
use GraphQL\Traits\HasParentTrait;
use GraphQL\Traits\IsStringableTrait;

/**
 * Class Fragment
 *
 * Holds plain fields (added with `use()`) and nested fields. A nested field is
 * created the same way as on a Node: `$fragment->friends(['first' => 2])` or
 * `$fragment->friends`. It returns the new Node, so you can keep chaining on it.
 *
 * @method FragmentInterface use (...$arguments)
 *
 * @package GraphQL\Entities
 */
class Fragment implements FragmentInterface
{
    use HasAttributesTrait;
    use HasNameTrait;
    use HasParentTrait;
    use IsStringableTrait;

    protected string $onType;

    /**
     * @var NodeInterface[]
     */
    protected array $children = [];

    public function __construct(string $name, string $onType)
    {
        $this->setName($name);
        $this->setOnType($onType);
    }

    /**
     * @param string $method
     * @param array  $arguments
     *
     * @return FragmentInterface|NodeInterface
     * @throws InvalidArgumentTypeException
     */
    public function __call(string $method, array $arguments)
    {
        if ($method !== 'use') {
            if (!isset($arguments[0])) {
                throw new InvalidArgumentTypeException('You must pass an Array as param 0');
            }

            if (!is_array($arguments[0])) {
                throw new InvalidArgumentTypeException(
                    is_scalar($arguments[0]) ? 'Non Array Scalar' : get_class($arguments[0])
                );
            }

            return $this->addChild($method, $arguments[0]);
        }

        foreach ($arguments as $argument) {
            if ($argument instanceof VariableInterface) {
                throw new InvalidArgumentTypeException(get_class($argument));
            }

            if ($argument instanceof InlineFragmentInterface) {
                throw new InvalidArgumentTypeException(get_class($argument));
            }

            if ($argument instanceof FragmentInterface) {
                throw new InvalidArgumentTypeException(get_class($argument));
            }

            $alias = new Alias($argument);
            $this->attributes[$alias->getKey()] = $alias;
        }

        return $this;
    }

    public function __get(string $name): NodeInterface
    {
        return $this->addChild($name);
    }

    /**
     * @return NodeInterface[]
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    public function hasChildren(): bool
    {
        return count($this->children) > 0;
    }

    public function getOnType(): string
    {
        return $this->onType;
    }

    public function setOnType(string $onType): FragmentInterface
    {
        $this->onType = $onType;

        return $this;
    }

    public function inline(): string
    {
        return '...' . $this->getName();
    }

    public function toString(): string
    {
        $body = $this->parseBody();

        if ($body === '') {
            return '';
        }

        return 'fragment ' . $this->getName() . ' on ' . $this->onType . ' {' . PHP_EOL
            . $body . PHP_EOL . '}' . PHP_EOL;
    }

    protected function addChild(string $name, array $arguments = []): NodeInterface
    {
        $this->children[$name] = new Node($name, $arguments);

        return $this->children[$name];
    }

    /**
     * Renders the plain fields followed by the nested fields, one per line.
     */
    protected function parseBody(): string
    {
        return implode(
            PHP_EOL,
            array_merge(
                array_map(fn(IsStringableInterface $item) => $item->toString(), $this->getAttributes()),
                array_map(fn(NodeInterface $node) => rtrim($node->parse()), $this->getChildren())
            )
        );
    }
}
