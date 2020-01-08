<?php


namespace tests\functional;


use GraphQL\Graph;
use GraphQL\Node;
use GraphQL\Mutation;
use PHPUnit\Framework\TestCase;

class HeroTest extends TestCase
{
    public function testHeroGet()
    {
        $hero = new Graph('hero', ['id' => '1']);
        $this->assertInstanceOf(Node::class, $hero);
        $hero->use('name', 'id');
        $this->assertEquals('{hero(id: "1") {name id}}', $hero->query(0, false));
        $friends = $hero->friends(['first' => 1])->use('name');
        $this->assertEquals('{hero(id: "1") {name id friends(first: 1) {name}}}', $hero->query(0, false));
        $parent = $friends->prev();
        $this->assertEquals($parent, $hero);
        $costumes = $parent->costumes->use('colour')->alias('color', 'colour');
        $this->assertEquals('{hero(id: "1") {name id friends(first: 1) {name} costumes {color: colour}}}', $hero->query(0, false));
        $costumes->alias('cosplay');
        $this->assertEquals('{hero(id: "1") {name id friends(first: 1) {name} cosplay: costumes {color: colour}}}', $hero->query(0, false));
    }
    
    public function testHeroPut()
    {
        $mutation = new Mutation('changeHeroCostumeColor', ['id' => 'theHeroId', 'color'=>'red']);
        $mutation
            ->hero
            ->use('name')
            ->costumes
            ->use('color');
        
        $this->assertEquals('mutation {changeHeroCostumeColor(id: "theHeroId", color: "red") {hero {name costumes {color}}}}', $mutation->query(0, false));
    }
}