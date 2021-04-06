<?php


namespace Tests\Unit\Entities;

use GraphQL\Contracts\Entities\AliasInterface;
use GraphQL\Contracts\Properties\IsStringableInterface;
use GraphQL\Entities\Alias;
use PHPUnit\Framework\TestCase;

final class AliasTest extends TestCase
{
    public function testInterfaces()
    {
        $alias = new Alias('foo', 'bar');
        $this->assertInstanceOf(AliasInterface::class, $alias);
        $this->assertInstanceOf(IsStringableInterface::class, $alias);
    }

    public function testSetGetAlias()
    {
        $alias = new Alias('foo');
        $this->assertNull($alias->getAlias());
        $this->assertEquals('foo', (string)$alias);
        $alias->setAlias('bar');
        $this->assertEquals('bar', $alias->getAlias());
        $this->assertEquals('bar: foo', (string)$alias);
    }

    public function testSetGetKey()
    {
        $alias = new Alias('foo');
        $this->assertIsString($alias->getKey());
        $this->assertEquals('foo', (string)$alias);
        $alias->setKey('bar');
        $this->assertEquals('bar', $alias->getKey());
        $this->assertEquals('bar', (string)$alias);
    }

    public function testToString()
    {
        $alias = new Alias('foo');
        $this->assertIsString($alias->__toString());
        $this->assertEquals('foo', $alias->__toString());
        $this->assertEquals((string)$alias, $alias->__toString());
    }
}
