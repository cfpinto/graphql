<?php


namespace GraphQL\Parsers;


use GraphQL\Contracts\Entities\InlineFragmentInterface;
use GraphQL\Contracts\Entities\RootNodeInterface;
use GraphQL\Contracts\Entities\VariableInterface;
use GraphQL\Contracts\Properties\IsParsableInterface;
use GraphQL\Actions\Mutation;
use GraphQL\Utils\Str;

class MutationParser extends NodeParser
{

    public function can(IsParsableInterface $parsable): bool
    {
        return ($parsable instanceof Mutation);
    }

    public function parse(IsParsableInterface $parsable, bool $singleLine = false): string
    {
        $str = '';

        if ($parsable instanceof RootNodeInterface) {
            if ($parsable->hasFragments()) {
                foreach ($parsable->getFragments() as $fragment) {
                    if (!$fragment instanceof InlineFragmentInterface) {
                        $str .= Str::ident($fragment->toString());
                    }
                }
            }

            $suffix = '';
            if ($parsable->hasVariables()) {
                $mutationName = ucfirst($parsable->getName()) . 'Mutation';
                $variables = implode(' ', array_map(fn(VariableInterface $item) => $item->toString(), $parsable->getVariables()));
                $varStr = PHP_EOL . "mutation {$mutationName}({$variables}) {" . PHP_EOL;
                $suffix = PHP_EOL . '}';
            } else {
                $varStr = 'mutation ';
            }

            $str .= PHP_EOL . Str::ident($varStr . parent::parse($parsable)) . $suffix;
        }

        if ($singleLine) {
            return Str::ugliffy($str);
        }

        return $str;
    }
}
