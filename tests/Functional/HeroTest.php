<?php


namespace Tests\Functional;


use GraphQL\Actions\Mutation;
use GraphQL\Actions\Query;
use GraphQL\Entities\Node;
use GraphQL\Utils\Str;
use PHPUnit\Framework\TestCase;

class HeroTest extends TestCase
{
    private Str $str;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->str = new Str();
    }

    public function testHeroGet()
    {
        $hero = new Query('hero', ['id' => '1']);
        $this->assertInstanceOf(Node::class, $hero);
        $hero->use('name', 'id');
        $this->assertEquals('{ hero(id: "1") { name id }}', $this->str->ugliffy($hero->toString()));
        $friends = $hero->friends(['first' => 1])->use('name');
        $this->assertEquals(
            '{ hero(id: "1") { name id friends(first: 1) { name }}}',
            $this->str->ugliffy($hero->toString())
        );
        $parent = $friends->prev();
        $this->assertEquals($parent, $hero);
        $costumes = $parent->costumes->use('colour')->alias('color', 'colour');
        $this->assertEquals(
            '{ hero(id: "1") { name id friends(first: 1) { name } costumes { color: colour }}}',
            $this->str->ugliffy($hero->toString())
        );
        $costumes->alias('cosplay');
        $this->assertEquals(
            '{ hero(id: "1") { name id friends(first: 1) { name } cosplay: costumes { color: colour }}}',
            $this->str->ugliffy($hero->toString())
        );
    }

    public function testHeroPut()
    {
        $mutation = new Mutation('changeHeroCostumeColor', ['id' => 'theHeroId', 'color' => 'red']);
        $mutation
            ->hero
            ->use('name')
            ->costumes
            ->use('color');

        $this->assertEquals(
            'mutation changeHeroCostumeColor(id: "theHeroId" color: "red") { hero { name costumes { color }}}',
            $this->str->ugliffy($mutation->toString())
        );
    }
}
