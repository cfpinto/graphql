<?php


namespace GraphQL\Actions;


use GraphQL\Contracts\Entities\RootNodeInterface;
use GraphQL\Entities\Node;
use GraphQL\Parsers\QueryParser;
use GraphQL\Traits\HasVariablesTrait;

class Query extends Node implements RootNodeInterface
{
    use HasVariablesTrait;

    public function __construct(string $name, array $arguments = [])
    {
        parent::__construct($name, $arguments);

        $this->setParsers([new QueryParser()]);

        $this->rootVariables();
    }
}
