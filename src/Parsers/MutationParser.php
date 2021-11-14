<?php


namespace GraphQL\Parsers;


use GraphQL\Contracts\Entities\InlineFragmentInterface;
use GraphQL\Contracts\Entities\RootNodeInterface;
use GraphQL\Contracts\Entities\VariableInterface;
use GraphQL\Contracts\Properties\IsParsableInterface;
use GraphQL\Actions\Mutation;

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
                        $str .= $this->strHelper->ident($fragment->toString());
                    }
                }
            }

            $suffix = '';
            $varStr = 'mutation ';

            if ($parsable->hasVariables()) {
                $mutationName = ucfirst($parsable->getName()) . 'Mutation';
                $variables = implode(
                    ' ',
                    array_map(
                        fn(VariableInterface $item) => $item->toString(),
                        $parsable->getVariables()
                    )
                );
                $varStr = PHP_EOL . "mutation {$mutationName}({$variables}) {" . PHP_EOL;
                $suffix = PHP_EOL . '}';
            }

            $str .= PHP_EOL . $this->strHelper->ident($varStr . parent::parse($parsable)) . $suffix;
        }

        if ($singleLine) {
            return $this->strHelper->ugliffy($str);
        }

        return $str;
    }
}
