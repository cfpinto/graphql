<?php


namespace GraphQL\Traits;


use GraphQL\Contracts\Entities\AliasInterface;
use GraphQL\Contracts\Properties\HasAliasInterface;
use GraphQL\Entities\Alias;

trait HasAliasTrait
{
    public function alias(string $alias, ?string $who = null): HasAliasInterface
    {
        if (is_null($who)) {
            $name = $this->getName();
            if ($name instanceof AliasInterface) {
                $name->setAlias($alias);
            } else {
                $name = new Alias($name, $alias);
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
