<?php
/**
 * Pokemon now graph api example app: https://react-relay-pokemon.now.sh/#/
 * Pokemon now graph api testing tool: https://graphql-pokemon.now.sh/?query=
 *
 * Test the generated query in the pokemon test api above
 */
require_once 'vendor/autoload.php';

$pokemon = new \GraphQL\Graph('pokemon', ['name' => 'Pikachu']);
$pokemon->use('id', 'number', 'name')
    ->alias('pikachu')
    ->alias('numb', 'number')
    ->attacks
    ->alias('pokettacks')
    ->special
    ->use('name', 'type', 'damage');

echo $pokemon;
