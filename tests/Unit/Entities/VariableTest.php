<?php


namespace Tests\Unit\Entities;


use GraphQL\Contracts\Entities\VariableInterface;
use GraphQL\Contracts\Properties\HasNameInterface;
use GraphQL\Contracts\Properties\IsStringableInterface;
use GraphQL\Entities\Variable;
use PHPUnit\Framework\TestCase;

class VariableTest extends TestCase
{
    public function testInterfaces()
    {
        $variable = new Variable('foo', 'String', 'bar');
        $this->assertInstanceOf(VariableInterface::class, $variable);
        $this->assertInstanceOf(IsStringableInterface::class, $variable);
        $this->assertInstanceOf(HasNameInterface::class, $variable);
    }

    public function testGettersAndSetters()
    {
        $variable = new Variable('foo', 'String', 'bar');
        $this->assertEquals('foo', $variable->getName());
        $this->assertEquals('bar', $variable->getDefault());
        $this->assertEquals('String', $variable->getType());
        $variable->setName('foo2');
        $variable->setType('Int');
        $variable->setDefault(1);
        $this->assertEquals('foo2', $variable->getName());
        $this->assertEquals(1, $variable->getDefault());
        $this->assertEquals('Int', $variable->getType());
    }

    public function testStringability()
    {
        $variable = new Variable('foo', 'String', 'bar');
        $this->assertEquals('$foo: String = "bar"', $variable->toString());
        $variable = new Variable('foo', 'String');
        $this->assertEquals('$foo: String', $variable->toString());
    }
}
