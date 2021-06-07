<?php


namespace GraphQL\Traits;


use GraphQL\Contracts\Entities\NodeInterface;
use GraphQL\Contracts\Entities\VariableInterface;
use GraphQL\Contracts\Properties\HasArgumentsInterface;
use GraphQL\Contracts\Properties\HasNodesInterface;
use GraphQL\Exceptions\InvalidTypeOperationException;

trait HasNodesTrait
{
    protected array $children = [];

    final public function getChildren(): array
    {
        return $this->children;
    }

    final public function addChild(string $key, NodeInterface $node): NodeInterface
    {
        $this->children[$key] = $node;
        $node->setParentNode($this);

        if ($node instanceof HasArgumentsInterface && $node->hasArguments()) {
            $node->rootVariables();
        }

        return $this->children[$key];
    }

    final public function hasChildren(): bool
    {
        return count($this->children) > 0;
    }

    final public function removeChild(string $key): HasNodesInterface
    {
        if (empty($this->children[$key])) {
            throw new InvalidTypeOperationException('null', NodeInterface::class);
        }

        unset($this->children[$key]);

        return $this;
    }

    final public function reindexChild(string $oldKey, string $newKey): void
    {
        $this->children[$newKey] = $this->children[$oldKey];
        unset($this->children[$oldKey]);
    }
}
