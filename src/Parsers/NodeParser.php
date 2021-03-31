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
        if ($parsable instanceof NodeInterface) {
            if ($parsable->getArguments()->count()) {
                $str = $parsable->getName() . '(' . $parsable->getArguments() . ') {' . PHP_EOL;
            } else {
                $str = $parsable->getName() . ' {' . PHP_EOL;
            }

            if ($parsable->hasAttributes()) {
                $str .= implode(
                    PHP_EOL,
                    array_map(fn(AliasInterface $alias) => $alias->toString(), $parsable->getAttributes())
                );
            }

            if ($parsable->hasFragments()) {
                $str .= PHP_EOL . implode(
                    PHP_EOL,
                    array_map(fn(FragmentInterface $fragment) => $fragment->inline(), $parsable->getFragments())
                );
            }

            if ($parsable->hasChildren()) {
                $str .= PHP_EOL . implode(
                        PHP_EOL,
                        array_map(fn(NodeInterface $node) => $node->parse(), $parsable->getChildren())
                    );
            }

            return $str . PHP_EOL . '}' . PHP_EOL;
        }

        return '';
    }
}
