<?php

namespace Tests\Unit\Parsers;

use GraphQL\Contracts\Entities\RootNodeInterface;
use GraphQL\Entities\Node;
use GraphQL\Entities\Variable;
use GraphQL\Parsers\NodeParser;
use GraphQL\Parsers\QueryParser;
use GraphQL\Traits\HasVariablesTrait;
use GraphQL\Utils\Str;
use PHPUnit\Framework\TestCase;

class QueryParserTest extends TestCase
{
    public function testInterfaces()
    {
        $parser = new QueryParser();
        $this->assertInstanceOf(NodeParser::class, $parser);
    }

    public function testRootNodeWithoutOperationNameUsesGeneratedName()
    {
        $root = new class('hero', ['name' => new Variable('name', 'String')]) extends Node implements RootNodeInterface {
            use HasVariablesTrait;
        };
        $root->use('id');

        $this->assertEquals(
            'query getHero($name: String){ hero(name: $name) { id }}',
            (new Str())->ugliffy((new QueryParser())->parse($root))
        );
    }
}
