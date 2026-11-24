<?php
/**
 * Created by PhpStorm.
 * User: claudiopinto
 * Date: 04/10/2017
 * Time: 14:48
 */

namespace GraphQL\Actions;

use GraphQL\Contracts\Entities\RootNodeInterface;
use GraphQL\Contracts\Properties\HasOperationNameInterface;
use GraphQL\Entities\Node;
use GraphQL\Parsers\MutationParser;
use GraphQL\Traits\HasOperationNameTrait;
use GraphQL\Traits\HasVariablesTrait;

/**
 * Class Mutation
 *
 * @package GraphQL
 */
class Mutation extends Node implements RootNodeInterface, HasOperationNameInterface
{
    use HasOperationNameTrait;
    use HasVariablesTrait;

    public function __construct(string $name, array $arguments = [])
    {
        parent::__construct($name, $arguments);

        $this->setParsers([new MutationParser()]);
    }
}
