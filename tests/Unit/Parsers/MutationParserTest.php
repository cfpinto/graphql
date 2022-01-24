<?php

namespace Tests\Unit\Parsers;

use GraphQL\Parsers\MutationParser;
use GraphQL\Parsers\NodeParser;
use PHPUnit\Framework\TestCase;

class MutationParserTest extends TestCase
{
    public function testInterfaces()
    {
        $parser = new MutationParser();
        $this->assertInstanceOf(NodeParser::class, $parser);
    }
}
