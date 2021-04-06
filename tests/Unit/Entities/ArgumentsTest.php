<?php
namespace Tests\Unit\Entities;

use GraphQL\Contracts\Entities\ArgumentInterface;
use GraphQL\Contracts\Properties\IsStringableInterface;
use GraphQL\Entities\Argument;
use PHPUnit\Framework\TestCase;

final class ArgumentsTest extends TestCase
{
    public function testInterfaces()
    {
        $argument = new Argument('foo');

        $this->assertInstanceOf(ArgumentInterface::class, $argument);
        $this->assertInstanceOf(IsStringableInterface::class, $argument);
    }

    public function testStringNoKeyArgument()
    {
        $argument = new Argument('foo');
        $this->assertEquals('foo', $argument->toString());
    }

    public function testKeyStringArgument()
    {
        $argument = new Argument('bar', 'foo');
        $this->assertEquals('foo: "bar"', $argument->toString());
    }

    public function testKeyNumberArgument()
    {
        $argument = new Argument(1, 'foo');
        $this->assertEquals('foo: 1', $argument->toString());
    }

    public function testKeyArrayArgument()
    {
        $argument = new Argument(['bar'], 'foo');
        $this->assertEquals('foo: ["bar"]', $argument->toString());
    }
}
