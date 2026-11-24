<?php


namespace Tests\Unit\Actions;


use GraphQL\Actions\Mutation;
use GraphQL\Contracts\Entities\RootNodeInterface;
use GraphQL\Entities\Variable;
use GraphQL\Utils\Str;
use GraphQL\Entities\Fragment;
use GraphQL\Entities\InlineFragment;
use PHPUnit\Framework\TestCase;

class MutationTest extends TestCase
{
    private Str $str;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->str = new Str();
    }

    public function testInterfaces()
    {
        $mutation = new Mutation('fooBar');
        $this->assertInstanceOf(RootNodeInterface::class, $mutation);
    }

    public function testMutation()
    {
        $mutation = new Mutation('fooBar', ['id' => 1, 'name' => 'bar']);
        $this->assertEquals('mutation { fooBar(id: 1 name: "bar") }', $this->str->ugliffy($mutation->toString()));
        $mutation->use('id', 'name');
        $this->assertEquals('mutation { fooBar(id: 1 name: "bar") { id name }}', $this->str->ugliffy($mutation->toString()));
        $mutation = new Mutation('fooBar2', ['name' => 'foo', 'test' => new Variable('bar', 'String')]);
        $mutation->use('id', 'name');
        $this->assertEquals(
            'mutation FooBar2Mutation($bar: String) { fooBar2(name: "foo" bar: $bar) { id name }}',
            $this->str->ugliffy($mutation->toString())
        );
    }

    public function testOperationNameWithVariables()
    {
        $mutation = new Mutation('changeName', ['name' => new Variable('name', 'String')]);
        $mutation->setOperationName('RenameHero')->use('id');

        $this->assertEquals(
            'mutation RenameHero($name: String) { changeName(name: $name) { id }}',
            $this->str->ugliffy($mutation->toString())
        );
    }

    public function testOperationNameWithoutVariables()
    {
        $mutation = new Mutation('changeName', ['name' => 'Luke']);
        $mutation->setOperationName('RenameHero')->use('id');

        $this->assertEquals(
            'mutation RenameHero { changeName(name: "Luke") { id }}',
            $this->str->ugliffy($mutation->toString())
        );
    }

    public function testSingleLine()
    {
        $mutation = new Mutation('createHero', ['name' => 'Luke']);
        $mutation->use('id');

        $this->assertEquals('mutation { createHero(name: "Luke") { id }}', $mutation->singleLine());
    }

    public function testFragmentIsDefinedBeforeMutation()
    {
        $fragment = new Fragment('heroFields', 'Hero');
        $fragment->use('id');
        $mutation = new Mutation('createHero', ['name' => 'Luke']);
        $mutation->use($fragment);

        $this->assertEquals(
            'fragment heroFields on Hero { id } mutation { createHero(name: "Luke") { ...heroFields }}',
            $this->str->ugliffy($mutation->toString())
        );
    }

    public function testInlineFragmentIsNotDefinedBeforeMutation()
    {
        $droid = new InlineFragment('Droid');
        $droid->use('primaryFunction');
        $mutation = new Mutation('createHero', ['name' => 'R2']);
        $mutation->use($droid);

        $this->assertEquals(
            'mutation { createHero(name: "R2") { ... on Droid { primaryFunction }}}',
            $this->str->ugliffy($mutation->toString())
        );
    }
}
