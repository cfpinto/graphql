<?php


namespace Tests\Unit\Collections;


use GraphQL\Collections\ArgumentsCollection;
use GraphQL\Contracts\Collections\StringableCollectionInterface;
use GraphQL\Contracts\Properties\HasVariablesInterface;
use GraphQL\Entities\Argument;
use GraphQL\Entities\Variable;
use PHPUnit\Framework\TestCase;

class TestArgumentsCollection extends TestCase
{
    private ArgumentsCollection $collection;

    protected function setUp(): void
    {
        parent::setUp();
        $arguments = [
            'foo' => 'bar',
            'test' => new Argument('value', 'test'),
        ];
        $this->collection = new ArgumentsCollection($arguments);
    }

    public function testInterfaces()
    {
        $this->assertInstanceOf(StringableCollectionInterface::class, $this->collection);
        $this->assertInstanceOf(HasVariablesInterface::class, $this->collection);
    }

    public function testStringable()
    {
        $this->assertIsString($this->collection->toString());
        $this->assertEquals('foo: "bar" test: "value"', $this->collection->toString());
    }

    public function testVariables()
    {
        $variable = new Variable('name', 'String');
        $this->collection->addVariable($variable);
        $this->assertCount(1, $this->collection->getVariables());
        $this->collection->removeVariable($variable);
        $this->assertFalse($this->collection->hasVariables());
    }

    public function testArguments()
    {
        $argument = new Argument('bar2', 'foo2');
        $this->collection->addArgument($argument);
        $this->assertCount(3, $this->collection->getArguments());
        $this->collection->removeArgument($argument);
        $this->assertCount(2, $this->collection->getArguments());
    }
}