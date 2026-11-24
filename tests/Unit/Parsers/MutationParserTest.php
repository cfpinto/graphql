<?php

namespace Tests\Unit\Parsers;

use GraphQL\Contracts\Entities\RootNodeInterface;
use GraphQL\Entities\Node;
use GraphQL\Entities\Variable;
use GraphQL\Parsers\MutationParser;
use GraphQL\Parsers\NodeParser;
use GraphQL\Traits\HasVariablesTrait;
use GraphQL\Utils\Str;
use PHPUnit\Framework\TestCase;

class MutationParserTest extends TestCase
{
    public function testInterfaces()
    {
        $parser = new MutationParser();
        $this->assertInstanceOf(NodeParser::class, $parser);
    }

    public function testRootNodeWithoutOperationNameUsesGeneratedName()
    {
        $root = new class('changeName', ['name' => new Variable('name', 'String')]) extends Node implements RootNodeInterface {
            use HasVariablesTrait;
        };
        $root->use('id');

        $this->assertEquals(
            'mutation ChangeNameMutation($name: String) { changeName(name: $name) { id }}',
            (new Str())->ugliffy((new MutationParser())->parse($root))
        );
    }
}
