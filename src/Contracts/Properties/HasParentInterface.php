<?php


namespace GraphQL\Contracts\Properties;

use GraphQL\Contracts\Entities\NodeInterface;
use GraphQL\Contracts\Entities\RootNodeInterface;

interface HasParentInterface
{
    public function getParentNode(): ?NodeInterface;

    public function setParentNode(NodeInterface $parentNode): HasParentInterface;

    public function getRootNode(): ?RootNodeInterface;

    /**
     * @alias getRootNode
     */
    public function root(): ?RootNodeInterface;

    /**
     * @alias getParentNode
     */
    public function prev(): ?NodeInterface;
}
