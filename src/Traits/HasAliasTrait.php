<?php


namespace GraphQL\Traits;


use GraphQL\Contracts\Entities\AliasInterface;
use GraphQL\Contracts\Properties\HasAliasInterface;
use GraphQL\Contracts\Properties\HasParentInterface;
use GraphQL\Entities\Alias;

trait HasAliasTrait
{
    public function alias(string $alias, ?string $who = null): HasAliasInterface
    {
        if ($who === null) {
            $oldKey = $this->getName();
            $name = new Alias($oldKey, $alias);

            if ($this instanceof HasParentInterface && $this->getParentNode()) {
                $this->getParentNode()
                    ->reindexChild($oldKey, (string)$name);
            }

            $this->setName($name);

            return $this;
        }

        $attributes = $this->getAttributes();
        if ($attributes[$who] instanceof AliasInterface) {
            $attributes[$who]->setAlias($alias);
        }

        return $this;
    }
}
