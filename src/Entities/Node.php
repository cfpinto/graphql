<?php


namespace GraphQL\Entities;


use GraphQL\Abstracts\NodeAbstract;
use GraphQL\Contracts\Entities\FragmentInterface;
use GraphQL\Contracts\Entities\InlineFragmentInterface;
use GraphQL\Contracts\Entities\NodeInterface;
use GraphQL\Contracts\Entities\VariableInterface;
use GraphQL\Exceptions\InvalidArgumentTypeException;
use GraphQL\Parsers\NodeParser;

class Node extends NodeAbstract
{
    public function __construct(string $name, array $arguments = [])
    {
        parent::__construct($name, $arguments, []);
        $this->parsers[] = new NodeParser();
    }

    protected function generate(string $name, array $arguments = []): NodeInterface
    {
        $keyName = $name;
        $className = __NAMESPACE__ . '\\' .
            str_replace('_', '', ucwords($name, '_'));

        if (class_exists($className)) {
            $node = new $className($name, $arguments);
        } else {
            $node = new Node($name, $arguments);
        }

        return $this->addChild($keyName, $node);
    }

    /**
     * @param array $arguments
     *
     * @return $this
     * @throws InvalidArgumentTypeException
     */
    protected function get(array $arguments): self
    {
        foreach ($arguments as $argument) {
            if ($argument instanceof VariableInterface) {
                throw new InvalidArgumentTypeException(get_class($argument));
            }

            if ($argument instanceof InlineFragmentInterface) {
                throw new InvalidArgumentTypeException(get_class($argument));
            }

            if ($argument instanceof FragmentInterface) {
                $this->root()->addFragment($argument);
                $argument->setParentNode($this);

                continue;
            }

            $alias = new Alias($argument);
            $this->attributes[$alias->getKey()] = $alias;
        }

        return $this;
    }
}
