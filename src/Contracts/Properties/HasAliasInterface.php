<?php


namespace GraphQL\Contracts\Properties;


use GraphQL\Contracts\Entities\AliasInterface;

interface HasAliasInterface
{
    public function alias(string $alias, ?string $who = null): HasAliasInterface;
}
