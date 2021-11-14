<?php


namespace GraphQL\Parsers;

use GraphQL\Contracts\Entities\AliasInterface;
use GraphQL\Contracts\Entities\FragmentInterface;
use GraphQL\Contracts\Entities\NodeInterface;
use GraphQL\Contracts\Parsers\ParserInterface;
use GraphQL\Contracts\Properties\IsParsableInterface;

class NodeParser implements ParserInterface
{

    public function can(IsParsableInterface $parsable): bool
    {
        return ($parsable instanceof NodeInterface);
    }

    public function parse(IsParsableInterface $parsable, bool $singleLine = false): string
    {
        return $parsable instanceof NodeInterface ?
            $this->parseArguments($parsable)
            . $this->parseAttributes($parsable)
            . $this->parseFragments($parsable)
            . $this->parseChildren($parsable)
            . PHP_EOL . '}' . PHP_EOL :
            '';
    }

    protected function parseArguments(NodeInterface $parsable): string
    {
        return $parsable->hasArguments() ?
            $parsable->getName() . '(' . $parsable->getArguments() . ') {' . PHP_EOL :
            $parsable->getName() . ' {' . PHP_EOL;
    }

    protected function parseAttributes(NodeInterface $parsable): string
    {
        return $parsable->hasAttributes() ?
            implode(
                PHP_EOL,
                array_map(fn(AliasInterface $alias) => $alias->toString(), $parsable->getAttributes())
            ) :
            '';
    }

    protected function parseFragments(NodeInterface $parsable): string
    {
        return $parsable->hasFragments() ?
            PHP_EOL . implode(
                PHP_EOL,
                array_map(fn(FragmentInterface $fragment) => $fragment->inline(), $parsable->getFragments())
            ) :
            '';
    }

    protected function parseChildren(NodeInterface $parsable): string
    {
        return $parsable->hasChildren() ?
            PHP_EOL . implode(
                PHP_EOL,
                array_map(fn(NodeInterface $node) => $node->parse(), $parsable->getChildren())
            ) : '';
    }
}
