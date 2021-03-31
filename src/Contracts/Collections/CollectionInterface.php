<?php


namespace GraphQL\Contracts\Collections;


interface CollectionInterface extends \Traversable, \Countable, \Iterator, \ArrayAccess
{
    public function clear(): void;
}
