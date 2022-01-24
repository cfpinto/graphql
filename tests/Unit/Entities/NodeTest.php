<?php

namespace Tests\Unit\Entities;

use GraphQL\Actions\Query;
use GraphQL\Collections\ArgumentsCollection;
use GraphQL\Contracts\Entities\NodeInterface;
use GraphQL\Contracts\Properties\HasAliasInterface;
use GraphQL\Contracts\Properties\HasArgumentsInterface;
use GraphQL\Contracts\Properties\HasAttributesInterface;
use GraphQL\Contracts\Properties\HasFragmentsInterface;
use GraphQL\Contracts\Properties\HasInlineFragmentsInterface;
use GraphQL\Contracts\Properties\HasNameInterface;
use GraphQL\Contracts\Properties\HasNodesInterface;
use GraphQL\Contracts\Properties\HasParentInterface;
use GraphQL\Contracts\Properties\IsParsableInterface;
use GraphQL\Contracts\Properties\IsStringableInterface;
use GraphQL\Entities\Fragment;
use GraphQL\Entities\Node;
use GraphQL\Entities\Variable;
use GraphQL\Exceptions\InvalidArgumentTypeException;
use GraphQL\Exceptions\InvalidTypeOperationException;
use GraphQL\Utils\Str;
use phpDocumentor\Reflection\Types\Callable_;
use PHPUnit\Framework\TestCase;
use Tests\Unit\AssertExceptionTrait;

class NodeTest extends TestCase
{
    use AssertExceptionTrait;

    private Str $str;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->str = new Str();
    }

    public function testInterfaces()
    {
        $node = new Node('foo');

        $this->assertInstanceOf(NodeInterface::class, $node);
        $this->assertInstanceOf(HasAliasInterface::class, $node);
        $this->assertInstanceOf(HasArgumentsInterface::class, $node);
        $this->assertInstanceOf(HasAttributesInterface::class, $node);
        $this->assertInstanceOf(HasFragmentsInterface::class, $node);
        $this->assertInstanceOf(HasInlineFragmentsInterface::class, $node);
        $this->assertInstanceOf(HasNameInterface::class, $node);
        $this->assertInstanceOf(HasNodesInterface::class, $node);
        $this->assertInstanceOf(HasParentInterface::class, $node);
        $this->assertInstanceOf(IsParsableInterface::class, $node);
        $this->assertInstanceOf(IsStringableInterface::class, $node);
    }

    public function testAlias()
    {
        $parent = new Query('root');
        $node = new Node('foo');
        $parent->addChild('foo', $node);
        $this->assertEquals('foo', $node->getName());
        $node->alias('bar');
        $this->assertEquals('bar: foo', $node->getName());
    }

    public function testArguments()
    {
        $query = new Query('foo');
        $node = new Node('foo', ['bar' => true]);
        $query->addChild('foo', $node);
        $this->assertTrue($node->hasArguments());
        $this->assertCount(1, $node->getArguments());
        $node->setArguments(new ArgumentsCollection(['bar' => true, 'foo' => new Variable('var', 'String')]));
        $this->assertTrue($node->hasArguments());
        $this->assertCount(2, $node->getArguments());
        $this->assertCount(1, $query->getVariables());
        $this->assertEquals('foo(bar: true var: $var) {}', $this->str->ugliffy($node->toString()));
        $this->assertEquals(
            'query getFoo($var: String){ foo { foo(bar: true var: $var) {}}}',
            $this->str->ugliffy($query->toString())
        );
    }

    public function testAttributes()
    {
        $node = new Node('foo');
        $node->use('name');
        $this->assertTrue($node->hasAttributes());
        $this->assertCount(1, $node->getAttributes());
        $this->assertEquals('foo { name }', $this->str->ugliffy($node->toString()));
    }

    public function testFragments()
    {
        $fragment = new Fragment('bar', 'BAR');
        $node = new Node('foo');
        $node->addFragment($fragment);
        $this->assertTrue($node->hasFragments());
        $this->assertCount(1, $node->getFragments());
        $this->assertEquals('foo { ...bar }', $this->str->ugliffy($node->toString()));
        $node->removeFragment($fragment);
        $this->assertFalse($node->hasFragments());
        $this->assertEquals('foo {}', $this->str->ugliffy($node->toString()));
        $node->on('foo')->use('bar');
        $this->assertEquals('foo { ... on foo { bar }}', $this->str->ugliffy($node->toString()));
    }

    public function testRelations()
    {
        $node = new Node('foo');
        $child = new Node('bar');
        $parent = new Node('parent');
        $root = new Query('root');
        $node->addChild('bar', $child);
        $this->assertTrue($node->hasChildren());
        $this->assertCount(1, $node->getChildren());
        $node->removeChild('bar');
        $this->assertFalse($node->hasChildren());
        $node->setParentNode($parent);
        $root->addChild('parent', $parent);
        $this->assertEquals($parent, $node->prev());
        $this->assertEquals($parent, $node->getParentNode());
        $this->assertEquals($root, $node->root());
        $this->assertEquals($root, $node->getRootNode());
        $node->bar2 = new Node('bar2');
        $this->assertEquals('foo { bar2 {}}', $this->str->ugliffy($node->parse()));
        $node->clear();
        $this->assertCount(0, $node->getChildren());
        $this->assertThrowsException(fn() => $node->bar2(), InvalidArgumentTypeException::class, 'Invalid argument type You must pass an Array as param 0');
        $this->assertThrowsException(fn() => $node->bar2(23), InvalidArgumentTypeException::class, 'Invalid argument type Non Array Scalar');
        $this->assertThrowsException(
            fn() => $node->removeChild('bar3'),
            InvalidTypeOperationException::class,
            'Invalid type null expected type GraphQL\Contracts\Entities\NodeInterface'
        );
    }

    public function testParseability()
    {
        $node = new Node('foo');
        $this->assertEquals('foo {}', $this->str->ugliffy($node->parse()));
        $node->use('uuid', 'name');
        $this->assertEquals('foo { uuid name }', $this->str->ugliffy($node->parse()));
        $node->on('bar')->use('catch');
        $this->assertEquals('foo { uuid name ... on bar { catch }}', $this->str->ugliffy($node->parse()));
        $this->assertEquals($node->query(), $node->parse());
        $this->assertCount(1, $node->getParsers());
        $this->assertEquals('foo { uuid name ... on bar { catch }}', $node->singleLine());
    }
}
