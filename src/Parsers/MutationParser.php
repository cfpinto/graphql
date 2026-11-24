<?php


namespace GraphQL\Parsers;


use GraphQL\Contracts\Entities\InlineFragmentInterface;
use GraphQL\Contracts\Entities\RootNodeInterface;
use GraphQL\Contracts\Entities\VariableInterface;
use GraphQL\Contracts\Properties\HasOperationNameInterface;
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

            $header = 'mutation';
            $operationName = $parsable instanceof HasOperationNameInterface ?
                $parsable->getOperationName() :
                null;

            if ($parsable->hasVariables()) {
                $mutationName = $operationName ?? ucfirst($parsable->getName()) . 'Mutation';
                $variables = implode(
                    ' ',
                    array_map(
                        fn(VariableInterface $item) => $item->toString(),
                        $parsable->getVariables()
                    )
                );
                $header .= " {$mutationName}({$variables})";
            } elseif ($operationName !== null) {
                $header .= " {$operationName}";
            }

            // The mutation field always sits inside the operation's selection set:
            // `mutation [Name[(vars)]] { field(args) { ... } }`
            $str .= PHP_EOL . $this->strHelper->ident(
                $header . ' {' . PHP_EOL . parent::parse($parsable) . PHP_EOL . '}'
            );
        }

        if ($singleLine) {
            return $this->strHelper->ugliffy($str);
        }

        return $str;
    }
}
