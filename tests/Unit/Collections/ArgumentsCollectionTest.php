<?php


namespace Tests\Unit\Collections;


use GraphQL\Collections\ArgumentsCollection;
use GraphQL\Contracts\Collections\StringableCollectionInterface;
use GraphQL\Contracts\Properties\HasVariablesInterface;
use GraphQL\Entities\Argument;
use GraphQL\Entities\Variable;
use GraphQL\Exceptions\InvalidArgumentTypeException;
use PHPUnit\Framework\TestCase;
use Tests\Unit\AssertExceptionTrait;

class ArgumentsCollectionTest extends TestCase
{
    use AssertExceptionTrait;

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
        $this->collection->addVariable($variable);
        $this->assertCount(1, $this->collection->getVariables());
        $this->collection->removeVariable($variable);
        $this->assertFalse($this->collection->hasVariables());
    }

    public function testArguments()
    {
        $argument = new Argument('bar2', 'foo2');
        $this->collection->addArgument($argument);
        $this->assertTrue($this->collection->hasArguments());
        $this->assertCount(3, $this->collection->getArguments());
        $this->assertEquals('foo', $this->collection->key());
        $this->collection->addArgument(new Argument('bar2', 'foo2'));
        $this->assertCount(3, $this->collection->getArguments());
        $this->collection->removeArgument($argument);
        $this->assertCount(2, $this->collection->getArguments());
        $this->collection->clear();
        $this->assertCount(0, $this->collection->getArguments());
    }

    public function testInvalidArguments()
    {
        $collection = new ArgumentsCollection(
            [
                'test' => new \stdClass()
            ]
        );
        $this->assertThrowsException(fn() => $collection->toString(), null, 'Invalid argument type stdClass');
    }

    public function testScalarTypes() {
        $collection = new ArgumentsCollection(
            [
                'test' => 1,
            ]
        );

        $this->assertEquals('test: 1', $collection->toString());
    }
}
