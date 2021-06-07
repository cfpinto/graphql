<?php


namespace GraphQL\Contracts\Properties;


use GraphQL\Contracts\Entities\NodeInterface;

interface HasNodesInterface
{
    /**
     * @return NodeInterface[]
     */
    public function getChildren(): array;

    public function addChild(string $key, NodeInterface $node): HasNodesInterface;

    public function hasChildren(): bool;

    public function removeChild(string $key): HasNodesInterface;

    public function reindexChild(string $oldKey, string $newKey): void;
}
