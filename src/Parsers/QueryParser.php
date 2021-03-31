<?php


namespace GraphQL\Parsers;


use GraphQL\Contracts\Entities\InlineFragmentInterface;
use GraphQL\Contracts\Entities\RootNodeInterface;
use GraphQL\Contracts\Entities\VariableInterface;
use GraphQL\Contracts\Properties\IsParsableInterface;
use GraphQL\Entities\Query;
use GraphQL\Utils\Str;

class QueryParser extends NodeParser
{

    public function can(IsParsableInterface $parsable): bool
    {
        return ($parsable instanceof Query);
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

            $varStr = '';
            if ($parsable->hasVariables()) {
                $className = (new \ReflectionClass($parsable))->getShortName();
                $variables = implode(' ', array_map(fn(VariableInterface $item) => $item->toString(), $parsable->getVariables()));
                $varStr = PHP_EOL . "query get{$className}({$variables})";
            }

            $str .= PHP_EOL . Str::ident($varStr . '{' . PHP_EOL . parent::parse($parsable) . PHP_EOL . '}' . PHP_EOL);
        }

        if ($singleLine) {
            return Str::ugliffy($str);
        }

        return $str;
    }
}
