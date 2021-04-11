<?php

namespace Tests\Unit\Actions;

use GraphQL\Actions\Query;
use GraphQL\Contracts\Entities\RootNodeInterface;
use GraphQL\Entities\Variable;
use GraphQL\Utils\Str;
use PHPUnit\Framework\TestCase;

class QueryTest extends TestCase {

    private Query $q;
    
    public function testInterfaces()
    {
        $query = new Query('foo');
        $this->assertInstanceOf(RootNodeInterface::class, $query);
    }

    public function testQuery()
    {
        $query = new Query('foo');
        $this->assertEquals('{ foo {}}', Str::ugliffy($query->toString()));
        $query->use('id', 'name');
        $this->assertEquals('{ foo { id name }}', Str::ugliffy($query->toString()));
        $query->allies(['name' => new Variable('name', 'String')]);
        $this->assertEquals(
            'query getFoo($name: String){ foo { id name allies(name: $name) {}}}',
            Str::ugliffy($query->toString())
        );
    }

}
