<?php


namespace GraphQL\Traits;


use GraphQL\Contracts\Entities\NodeInterface;
use GraphQL\Contracts\Entities\RootNodeInterface;
use GraphQL\Contracts\Properties\HasParentInterface;

trait HasParentTrait
{
    protected ?NodeInterface $parent = null;

    final public function setParentNode(NodeInterface $parentNode): HasParentInterface
    {
        $this->parent = $parentNode;

        return $this;
    }

    final public function getParentNode(): ?NodeInterface
    {
        return $this->parent;
    }

    final public function getRootNode(): ?RootNodeInterface
    {
        if ($this instanceof RootNodeInterface) {
            return $this;
        }

        $root = null;
        $current = $this;
        while ($current = $current->getParentNode()) {
            if ($current instanceof RootNodeInterface) {
                $root = $current;
            }
        }

        return $root;
    }

    final public function prev(): ?NodeInterface
    {
        return $this->getParentNode();
    }

    final public function root(): ?RootNodeInterface
    {
        return $this->getRootNode();
    }
}
