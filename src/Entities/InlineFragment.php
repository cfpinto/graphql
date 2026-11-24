<?php


namespace GraphQL\Entities;


use GraphQL\Contracts\Entities\InlineFragmentInterface;

class InlineFragment extends Fragment implements InlineFragmentInterface
{
    public function __construct(string $onType, string $name = '')
    {
        parent::__construct($name, $onType);
    }

    public function toString(): string
    {
        $body = $this->parseBody();

        if ($body === '') {
            return '';
        }

        return '... on ' . $this->getOnType() . ' {' . PHP_EOL
            . $body . PHP_EOL . '}' . PHP_EOL;
    }

    public function inline(): string
    {
        return $this->toString();
    }
}
