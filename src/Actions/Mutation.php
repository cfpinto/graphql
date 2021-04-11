<?php
/**
 * Created by PhpStorm.
 * User: claudiopinto
 * Date: 04/10/2017
 * Time: 14:48
 */

namespace GraphQL\Actions;

use GraphQL\Contracts\Entities\RootNodeInterface;
use GraphQL\Entities\Node;
use GraphQL\Parsers\MutationParser;
use GraphQL\Traits\HasVariablesTrait;

/**
 * Class Mutation
 *
 * @package GraphQL
 */
class Mutation extends Node implements RootNodeInterface
{
    use HasVariablesTrait;

    public function __construct(string $name, array $arguments = [])
    {
        parent::__construct($name, $arguments);

        $this->setParsers([new MutationParser()]);
    }
}
