<?php

namespace Tests\Unit\Actions;

use GraphQL\Actions\Query;
use GraphQL\Contracts\Entities\RootNodeInterface;
use GraphQL\Contracts\Properties\HasOperationNameInterface;
use GraphQL\Entities\Variable;
use GraphQL\Utils\Str;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class QueryTest extends TestCase
{
    private Str $str;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->str = new Str();
    }


    public function testInterfaces()
    {
        $query = new Query('foo');
        $this->assertInstanceOf(RootNodeInterface::class, $query);
    }

    public function testQuery()
    {
        $variable = new Variable('foo', 'String');
        $query = new Query('foo');
        $this->assertEquals('{ foo }', $this->str->ugliffy($query->toString()));
        $query->use('id', 'name');
        $this->assertEquals('{ foo { id name }}', $this->str->ugliffy($query->toString()));
        $query->allies(['name' => new Variable('name', 'String')]);
        $this->assertEquals(
            'query getFoo($name: String){ foo { id name allies(name: $name) }}',
            $this->str->ugliffy($query->toString())
        );
        $query->addVariable($variable);
        $this->assertCount(2, $query->getVariables());
        $query->addVariable($variable);
        $this->assertCount(2, $query->getVariables());
        $query->removeVariable($variable);
        $this->assertCount(1, $query->getVariables());
    }

    public function testOperationNameWithVariables()
    {
        $query = new Query('hero', ['name' => new Variable('name', 'String')]);
        $result = $query->setOperationName('getMyHeroName');

        $this->assertSame($query, $result);
        $this->assertInstanceOf(HasOperationNameInterface::class, $query);
        $this->assertTrue($query->hasOperationName());
        $this->assertEquals('getMyHeroName', $query->getOperationName());
        $this->assertEquals(
            'query getMyHeroName($name: String){ hero(name: $name) { name }}',
            $this->str->ugliffy($query->use('name')->toString())
        );
    }

    public function testOperationNameWithoutVariables()
    {
        $query = (new Query('hero', ['id' => 1]))->setOperationName('HeroById');

        $this->assertEquals(
            'query HeroById { hero(id: 1) { name }}',
            $this->str->ugliffy($query->use('name')->toString())
        );
    }

    public function testOperationNameCanBeReset()
    {
        $query = new Query('hero', ['name' => new Variable('name', 'String')]);
        $query->setOperationName('getMyHeroName')->setOperationName(null);

        $this->assertFalse($query->hasOperationName());
        $this->assertEquals(
            'query getHero($name: String){ hero(name: $name) { name }}',
            $this->str->ugliffy($query->use('name')->toString())
        );
    }

    public function testInvalidOperationName()
    {
        $this->expectException(InvalidArgumentException::class);

        (new Query('hero'))->setOperationName('my hero');
    }

    public function testSingleLine()
    {
        $query = new Query('hero', ['id' => 1]);
        $query->use('name');

        $this->assertEquals('{ hero(id: 1) { name }}', $query->singleLine());
    }
}
