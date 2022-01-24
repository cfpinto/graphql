<?php

namespace Tests\Unit\Actions;

use GraphQL\Actions\Query;
use GraphQL\Contracts\Entities\RootNodeInterface;
use GraphQL\Entities\Variable;
use GraphQL\Utils\Str;
use PHPUnit\Framework\TestCase;

class QueryTest extends TestCase
{
    private Str $str;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->str = new Str();
    }


    public function testInterfaces()
    {
        $query = new Query('foo');
        $this->assertInstanceOf(RootNodeInterface::class, $query);
    }

    public function testQuery()
    {
        $variable = new Variable('foo', 'String');
        $query = new Query('foo');
        $this->assertEquals('{ foo {}}', $this->str->ugliffy($query->toString()));
        $query->use('id', 'name');
        $this->assertEquals('{ foo { id name }}', $this->str->ugliffy($query->toString()));
        $query->allies(['name' => new Variable('name', 'String')]);
        $this->assertEquals(
            'query getFoo($name: String){ foo { id name allies(name: $name) {}}}',
            $this->str->ugliffy($query->toString())
        );
        $query->addVariable($variable);
        $this->assertCount(2, $query->getVariables());
        $query->addVariable($variable);
        $this->assertCount(2, $query->getVariables());
        $query->removeVariable($variable);
        $this->assertCount(1, $query->getVariables());
    }

}
