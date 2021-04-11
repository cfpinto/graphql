<?php
/**
 * Pokemon now graph api example app: https://react-relay-pokemon.now.sh/#/
 * Pokemon now graph api testing tool: https://graphql-pokemon.now.sh/?query=
 *
 * Test the generated query in the pokemon test api above
 */
require_once 'vendor/autoload.php';

$hero = new \GraphQL\Graph('hero');
echo $hero->use('name')
    ->friends
        ->use('name')
    ->root()
    ->query();

$hero = new \GraphQL\Graph('hero');
echo $hero->use('name')
    ->friends(['first' => 2])
        ->use('name')
    ->root()
    ->query();

$hero = new \GraphQL\Graph('hero');
echo $hero->use('name')
    ->friends(['first' => 2])
        ->use('name')
    ->prev()
    ->costumes
        ->color
    ->root()
    ->query();

$hero = new \GraphQL\Graph('hero');
echo $hero->use('name')
    ->on('FlyingHero')
        ->use('hasCape')
    ->prev()
    ->on('StrongHero')
        ->use('strengthLevel')
    ->prev()
    ->friends(['first'=>2])
        ->use('name')
    ->prev()
    ->costumes
        ->color
    ->root()
        ->query();

$hero = new \GraphQL\Graph('hero');
echo $hero->use('name')
    ->alias('call_me_this', 'name')
    ->friends(['first'=>2])
        ->alias('partners_in_good')
        ->use('name')
    ->prev()
    ->costumes
        ->use('color')
    ->root()
        ->query();

$fragment = new \GraphQL\Fragment('properties', 'Hero');
$fragment->use('id', 'age');
$hero = new \GraphQL\Graph('hero');
echo $hero->use('name', $fragment)->query();

$variable = new \GraphQL\Variable('name', 'String');
$hero = new \GraphQL\Graph('hero', ['name' => $variable]);
echo $hero->use('name')->query();

$variable = new \GraphQL\Variable('name', 'String');
$hero = new \GraphQL\Graph('hero', ['name' => $variable]);
echo $hero->use('name', '__typename')->query();

$variable = new \GraphQL\Variable('name', 'String');
$hero = new \GraphQL\Graph('hero', ['name' => $variable]);
echo $hero->use('name', '__typename')
    ->alias('type', '__typename')
    ->query();

$mutation = new GraphQL\Mutation('changeHeroCostumeColor', ['id' => 'theHeroId', 'color'=>'red']);
echo $mutation
    ->hero
    ->use('name')
    ->costumes
    ->use('color')
    ->root()
    ->query();

$mutation = new GraphQL\Mutation('changeHeroCostumeColor', ['id' => new GraphQL\Variable('uuid', 'String', ''), new GraphQL\Variable('color', 'String', '')]);
echo $mutation
    ->hero
    ->use('name')
    ->costumes
    ->use('color')
    ->root()
    ->query();
