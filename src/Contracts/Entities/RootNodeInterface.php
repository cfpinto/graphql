<?php


namespace GraphQL\Contracts\Entities;


use GraphQL\Contracts\Properties\HasFragmentsInterface;
use GraphQL\Contracts\Properties\HasVariablesInterface;

interface RootNodeInterface extends NodeInterface, HasVariablesInterface, HasFragmentsInterface
{
}
