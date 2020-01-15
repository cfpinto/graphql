<?php
/**
 * Pokemon now graph api example app: https://react-relay-pokemon.now.sh/#/
 * Pokemon now graph api testing tool: https://graphql-pokemon.now.sh/?query=
 *
 * Test the generated query in the pokemon test api above
 */
require_once 'vendor/autoload.php';

$variable = new \GraphQL\Variable('name', 'String', 'Pikachu');
$fragment = new \GraphQL\Fragment('pokemonFields', 'Pokemon');
$fragment->use('number', 'name');
$pokemon = new \GraphQL\Graph('pokemon', ['name' => $variable]);
$pokemon->use('id', $fragment)
    ->attacks
    ->special(['name' => $variable])
    ->use('name', 'type', 'damage');

echo $pokemon;
//echo $fragment;
