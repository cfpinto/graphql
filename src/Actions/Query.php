<?php


namespace GraphQL\Actions;


use GraphQL\Contracts\Entities\RootNodeInterface;
use GraphQL\Contracts\Properties\HasOperationNameInterface;
use GraphQL\Entities\Node;
use GraphQL\Parsers\QueryParser;
use GraphQL\Traits\HasOperationNameTrait;
use GraphQL\Traits\HasVariablesTrait;

class Query extends Node implements RootNodeInterface, HasOperationNameInterface
{
    use HasOperationNameTrait;
    use HasVariablesTrait;

    public function __construct(string $name, array $arguments = [])
    {
        parent::__construct($name, $arguments);

        $this->setParsers([new QueryParser()]);

        $this->rootVariables();
    }
}
