<?php


namespace Tests\Unit\Entities;

use GraphQL\Actions\Query;
use GraphQL\Contracts\Entities\FragmentInterface;
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

class FragmentTest extends TestCase
{
    private Str $str;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->str = new Str();
    }

    public function testInterfaces()
    {
        $fragment = new Fragment('foo', 'bar');

        $this->assertInstanceOf(FragmentInterface::class, $fragment);
        $this->assertInstanceOf(HasParentInterface::class, $fragment);
        $this->assertInstanceOf(HasNameInterface::class, $fragment);
        $this->assertInstanceOf(HasAttributesInterface::class, $fragment);
    }

    public function testUse()
    {
        $fragment = new Fragment('foo', 'bar');
        $fragment->use('name', 'age');

        $this->assertTrue($fragment->hasAttributes());
        $this->assertCount(2, $fragment->getAttributes());
    }

    public function testItFailsWithVariableAsAttribute()
    {
        $this->expectException(InvalidArgumentTypeException::class);

        $fragment = new Fragment('foo', 'bar');
        $fragment->use(new Variable('name', 'String'));
    }

    public function testItFailsWithInlineFragmentAsAttribute()
    {
        $this->expectException(InvalidArgumentTypeException::class);

        $fragment = new Fragment('foo', 'bar');
        $fragment->use(new InlineFragment('bar', 'foo'));
    }

    public function testItFailsWithFragmentAsAttribute()
    {
        $this->expectException(InvalidArgumentTypeException::class);

        $fragment = new Fragment('foo', 'bar');
        $fragment->use(new Fragment('bar', 'foo'));
    }

    public function testCanHandleName()
    {
        $fragment = new Fragment('foo', 'bar');
        $this->assertEquals('foo', $fragment->getName());
        $fragment->setName('foo2');
        $this->assertEquals('foo2', $fragment->getName());
    }

    public function testCanHandleParents()
    {
        $query = new Query('hero');
        $fragment = new Fragment('foo', 'bar');
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
        $fragment = new Fragment('foo', 'Bar');
        $this->assertEquals('', $fragment->toString());
        $fragment->use('name');
        $this->assertEquals('fragment foo on Bar { name }', $this->str->ugliffy($fragment->toString()));
        $this->assertEquals('...foo', $fragment->inline());
    }

    public function testNestedFieldWithArguments()
    {
        $fragment = new Fragment('hero', 'Hero');
        $fragment->use('name');
        $friends = $fragment->friends(['first' => 2]);
        $friends->use('name');

        $this->assertInstanceOf(Node::class, $friends);
        $this->assertTrue($fragment->hasChildren());
        $this->assertSame(['friends' => $friends], $fragment->getChildren());
        $this->assertEquals(
            'fragment hero on Hero { name friends(first: 2) { name }}',
            $this->str->ugliffy($fragment->toString())
        );
    }

    public function testNestedFieldWithoutArguments()
    {
        $fragment = new Fragment('hero', 'Hero');
        $fragment->costumes->use('color');

        $this->assertEquals(
            'fragment hero on Hero { costumes { color }}',
            $this->str->ugliffy($fragment->toString())
        );
    }

    public function testDeeplyNestedFields()
    {
        $fragment = new Fragment('hero', 'Hero');
        $fragment->friends(['first' => 2])
            ->use('name')
            ->costumes(['season' => 1])
            ->use('color');

        $this->assertEquals(
            'fragment hero on Hero { friends(first: 2) { name costumes(season: 1) { color }}}',
            $this->str->ugliffy($fragment->toString())
        );
    }

    public function testNestedFieldFailsWithNonArrayArgument()
    {
        $this->expectException(InvalidArgumentTypeException::class);

        $fragment = new Fragment('hero', 'Hero');
        $fragment->friends(2);
    }

    public function testNestedFieldFailsWithoutArgument()
    {
        $this->expectException(InvalidArgumentTypeException::class);

        $fragment = new Fragment('hero', 'Hero');
        $fragment->friends();
    }

    public function testInlineFragmentWithNestedField()
    {
        $fragment = new InlineFragment('Droid');
        $fragment->use('primaryFunction');
        $fragment->friends(['first' => 1])->use('name');

        $this->assertEquals(
            '... on Droid { primaryFunction friends(first: 1) { name }}',
            $this->str->ugliffy($fragment->toString())
        );
    }
}
