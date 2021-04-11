<?php


namespace Tests\Unit\Actions;


use GraphQL\Actions\Mutation;
use GraphQL\Contracts\Entities\RootNodeInterface;
use GraphQL\Entities\Variable;
use GraphQL\Utils\Str;
use PHPUnit\Framework\TestCase;

class MutationTest extends TestCase
{
    public function testInterfaces()
    {
        $mutation = new Mutation('fooBar');
        $this->assertInstanceOf(RootNodeInterface::class, $mutation);
    }

    public function testMutation()
    {
        $mutation = new Mutation('fooBar', ['id' => 1, 'name' => 'bar']);
        $this->assertEquals('mutation fooBar(id: 1 name: "bar") {}', Str::ugliffy($mutation->toString()));
        $mutation->use('id', 'name');
        $this->assertEquals('mutation fooBar(id: 1 name: "bar") { id name }', Str::ugliffy($mutation->toString()));
        $mutation = new Mutation('fooBar2', ['name' => 'foo', 'test' => new Variable('bar', 'String')]);
        $mutation->use('id', 'name');
        $this->assertEquals(
            'mutation FooBar2Mutation($bar: String) { fooBar2(name: "foo" bar: $bar) { id name }}',
            Str::ugliffy($mutation->toString())
        );
    }
}
