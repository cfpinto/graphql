<?php


namespace Tests\Unit\Entities;

use GraphQL\Actions\Query;
use GraphQL\Contracts\Entities\FragmentInterface;
use GraphQL\Contracts\Entities\InlineFragmentInterface;
use GraphQL\Contracts\Properties\HasAttributesInterface;
use GraphQL\Contracts\Properties\HasNameInterface;
use GraphQL\Contracts\Properties\HasParentInterface;
use GraphQL\Entities\Fragment;
use GraphQL\Entities\InlineFragment;
use GraphQL\Entities\Node;
use GraphQL\Entities\Variable;
use GraphQL\Exceptions\InvalidArgumentTypeException;
use GraphQL\Utils\Str;
use PHPUnit\Framework\TestCase;

class InlineFragmentTest extends TestCase
{
    private Str $str;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->str = new Str();
    }

    public function testInterfaces()
    {
        $fragment = new InlineFragment('foo');

        $this->assertInstanceOf(InlineFragmentInterface::class, $fragment);
        $this->assertInstanceOf(FragmentInterface::class, $fragment);
        $this->assertInstanceOf(HasParentInterface::class, $fragment);
        $this->assertInstanceOf(HasNameInterface::class, $fragment);
        $this->assertInstanceOf(HasAttributesInterface::class, $fragment);
    }

    public function testUse()
    {
        $fragment = new InlineFragment('foo', 'bar');
        $fragment->use('name', 'age');

        $this->assertTrue($fragment->hasAttributes());
        $this->assertCount(2, $fragment->getAttributes());
    }

    public function testItFailsWithVariableAsAttribute()
    {
        $this->expectException(InvalidArgumentTypeException::class);

        $fragment = new InlineFragment('foo', 'bar');
        $fragment->use(new Variable('name', 'String'));
    }

    public function testItFailsWithInlineFragmentAsAttribute()
    {
        $this->expectException(InvalidArgumentTypeException::class);

        $fragment = new InlineFragment('foo', 'bar');
        $fragment->use(new InlineFragment('bar', 'foo'));
    }

    public function testItFailsWithFragmentAsAttribute()
    {
        $this->expectException(InvalidArgumentTypeException::class);

        $fragment = new InlineFragment('foo', 'bar');
        $fragment->use(new Fragment('bar', 'foo'));
    }

    public function testCanHandleName()
    {
        $fragment = new InlineFragment('foo', 'bar');
        $this->assertEquals('bar', $fragment->getName());
        $fragment->setName('foo2');
        $this->assertEquals('foo2', $fragment->getName());
    }

    public function testCanHandleParents()
    {
        $query = new Query('hero');
        $fragment = new InlineFragment('foo', 'bar');
        $node = new Node('hero');
        $fragment->setParentNode($node);
        $this->assertEquals($node, $fragment->getParentNode());
        $this->assertEquals($node, $fragment->prev());
        $this->assertNull($fragment->getRootNode());
        $this->assertNull($fragment->root());
        $node->setParentNode($query);
        $this->assertEquals($query, $fragment->getRootNode());
        $this->assertEquals($query, $fragment->root());
    }

    public function testIsStringable()
    {
        $fragment = new InlineFragment('foo', 'Bar');
        $this->assertEquals('', $fragment->toString());
        $fragment->use('name');
        $this->assertEquals('... on foo { name }', $this->str->ugliffy($fragment->toString()));
        $this->assertEquals('... on foo { name }', $this->str->ugliffy($fragment->inline()));
    }
}
