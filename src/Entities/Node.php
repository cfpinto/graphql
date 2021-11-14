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
    /**
     * Node constructor.
     * Version 2 will force presenters as args
     *
     * @param string $name
     * @param array  $arguments
     * @param array  $presenters
     *
     * @todo introduce factories
     * @todo force parsers as args or setter
     */
    public function __construct(string $name, array $arguments = [], array $presenters = [])
    {
        parent::__construct($name, $arguments, $presenters);

        $this->parsers[] = new NodeParser();
    }

    protected function generate(string $name, array $arguments = []): NodeInterface
    {
        $className = __NAMESPACE__ . '\\' .
            str_replace('_', '', ucwords($name, '_'));

        return $this->addChild(
            $name,
            class_exists($className) ?
                new $className($name, $arguments):
                new Node($name, $arguments)
        );
    }

    /**
     * @param array $arguments
     *
     * @return $this
     * @throws InvalidArgumentTypeException
     */
    protected function useM(array $arguments): self
    {
        foreach ($arguments as $argument) {
            if ($argument instanceof VariableInterface) {
                throw new InvalidArgumentTypeException(get_class($argument));
            }

            if ($argument instanceof InlineFragmentInterface) {
                $this->on($argument);

                continue;
            }

            if ($argument instanceof FragmentInterface) {
                $this->root()->addFragment($argument);
                $argument->setParentNode($this);

                continue;
            }

            $alias = is_array($argument) ?
                new Alias(key($argument), current($argument)):
                new Alias($argument);

            $this->attributes[$alias->getKey()] = $alias;
        }

        return $this;
    }
}
