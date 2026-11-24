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

    public function testKeyListArgumentKeepsFormat()
    {
        $argument = new Argument(['bar', 1, true, null], 'foo');
        $this->assertEquals('foo: ["bar",1,true,null]', $argument->toString());
    }

    public function testKeyEmptyArrayArgument()
    {
        $argument = new Argument([], 'foo');
        $this->assertEquals('foo: []', $argument->toString());
    }

    public function testKeyObjectArgumentHasUnquotedKeys()
    {
        $argument = new Argument(['vector' => 'test', 'certainty' => 0.7], 'nearVector');
        $this->assertEquals('nearVector: {vector: "test", certainty: 0.7}', $argument->toString());
    }

    public function testKeyNestedObjectArgument()
    {
        $argument = new Argument(
            [
                'and' => [
                    ['name' => ['eq' => 'Luke']],
                    ['age' => ['gt' => 18]],
                ],
                'active' => false,
            ],
            'where'
        );
        $this->assertEquals(
            'where: {and: [{name: {eq: "Luke"}},{age: {gt: 18}}], active: false}',
            $argument->toString()
        );
    }

    public function testKeyObjectArgumentEscapesStrings()
    {
        $argument = new Argument(['text' => 'say "hi"'], 'filter');
        $this->assertEquals('filter: {text: "say \\"hi\\""}', $argument->toString());
    }

    public function testNestedEnumArgument()
    {
        $argument = new Argument(['field' => ArgumentsTestEnum::NAME, 'direction' => ArgumentsTestEnum::ASC], 'orderBy');
        $this->assertEquals('orderBy: {field: NAME, direction: ASC}', $argument->toString());
    }

    public function testNoKeyObjectArgument()
    {
        $argument = new Argument(['foo' => 'bar']);
        $this->assertEquals('{foo: "bar"}', $argument->toString());
    }

    public function testGetters()
    {
        $argument = new Argument('bar', 'foo');
        $this->assertEquals('foo', $argument->getKey());
        $this->assertEquals('bar', $argument->getValue());

        $argument = new Argument('bar');
        $this->assertNull($argument->getKey());
    }
}

enum ArgumentsTestEnum
{
    case NAME;
    case ASC;
}
