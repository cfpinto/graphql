#!/usr/bin/php
<?php

/**
 * Pokemon now graph api example app: https://react-relay-pokemon.now.sh/#/
 * Pokemon now graph api testing tool: https://graphql-pokemon.now.sh/?query=
 *
 * Test the generated query in the pokemon test api above
 */
require_once 'vendor/autoload.php';

$hero = new \GraphQL\Actions\Query('hero');
printf(
    '%s',
    $hero->use('name')
        ->friends
        ->use('name')
        ->root()
        ->query()
);

$hero = new \GraphQL\Actions\Query('hero');
printf(
    '%s',
    $hero->use('name')
        ->friends(['first' => 2])
        ->use('name')
        ->root()
        ->query()
);

$hero = new \GraphQL\Actions\Query('hero');
printf(
    '%s',
    $hero->use('name')
        ->friends(['first' => 2])
        ->use('name')
        ->prev()
        ->costumes
        ->color
        ->root()
        ->query()
);

$hero = new \GraphQL\Actions\Query('hero');
printf(
    '%s',
    $hero->use('name')
        ->on('FlyingHero')
        ->use('hasCape')
        ->prev()
        ->on('StrongHero')
        ->use('strengthLevel')
        ->prev()
        ->friends(['first' => 2])
        ->use('name')
        ->prev()
        ->costumes
        ->color
        ->root()
        ->query()
);

$hero = new \GraphQL\Actions\Query('hero');
printf(
    '%s',
    $hero->use('name')
        ->alias('call_me_this', 'name')
        ->friends(['first' => 2])
        ->alias('partners_in_good')
        ->use('name')
        ->prev()
        ->costumes
        ->use('color')
        ->root()
        ->query()
);

$fragment = new \GraphQL\Entities\Fragment('properties', 'Hero');
$fragment->use('id', 'age');
$hero = new \GraphQL\Actions\Query('hero');
printf('%s', $hero->use('name', $fragment)->query());

$variable = new \GraphQL\Entities\Variable('name', 'String');
$hero = new \GraphQL\Actions\Query('hero', ['name' => $variable]);
printf('%s', $hero->use('name')->query());

$variable = new \GraphQL\Entities\Variable('name', 'String');
$hero = new \GraphQL\Actions\Query('hero', ['name' => $variable]);
printf('%s', $hero->use('name', '__typename')->query());

$variable = new \GraphQL\Entities\Variable('name', 'String');
$hero = new \GraphQL\Actions\Query('hero', ['name' => $variable]);
printf(
    '%s',
    $hero->use('name', '__typename')
        ->alias('type', '__typename')
        ->query()
);

$mutation = new GraphQL\Actions\Mutation('changeHeroCostumeColor', ['id' => 'theHeroId', 'color' => 'red']);
printf(
    '%s',
    $mutation
        ->hero
        ->use('name')
        ->costumes
        ->use('color')
        ->root()
        ->query()
);

$mutation = new GraphQL\Actions\Mutation(
    'changeHeroCostumeColor',
    [
        'id' => new GraphQL\Entities\Variable('uuid', 'String', ''),
        new GraphQL\Entities\Variable('color', 'String', '')
    ]
);
printf(
    '%s',
    $mutation
        ->hero
        ->use('name')
        ->costumes
        ->use('color')
        ->root()
        ->query()
);
