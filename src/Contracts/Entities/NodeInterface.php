<?php


namespace GraphQL\Contracts\Entities;


use GraphQL\Contracts\Properties\HasArgumentsInterface;
use GraphQL\Contracts\Properties\HasAttributesInterface;
use GraphQL\Contracts\Properties\HasFragmentsInterface;
use GraphQL\Contracts\Properties\HasInlineFragmentsInterface;
use GraphQL\Contracts\Properties\HasNameInterface;
use GraphQL\Contracts\Properties\HasNodesInterface;
use GraphQL\Contracts\Properties\HasParentInterface;
use GraphQL\Contracts\Properties\IsParsableInterface;
use GraphQL\Contracts\Properties\IsStringableInterface;

interface NodeInterface extends HasParentInterface, HasNameInterface, IsParsableInterface
    , IsStringableInterface, HasNodesInterface, HasArgumentsInterface, HasFragmentsInterface
    , HasInlineFragmentsInterface, HasAttributesInterface
{
    public function clear(): NodeInterface;
}
