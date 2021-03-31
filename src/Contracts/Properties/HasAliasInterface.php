<?php


namespace GraphQL\Contracts\Properties;


use GraphQL\Contracts\Entities\AliasInterface;

interface HasAliasInterface
{
    public function getKeyName(): AliasInterface;

    public function setKeyName(string $keyName): HasAliasInterface;

    public function alias(string $alias, ?string $who): HasAliasInterface;
}
