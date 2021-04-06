<?php


namespace GraphQL\Entities;


use GraphQL\Contracts\Entities\InlineFragmentInterface;
use GraphQL\Contracts\Properties\IsStringableInterface;

class InlineFragment extends Fragment implements InlineFragmentInterface
{
    public function __construct(string $onType, string $name = '')
    {
        parent::__construct($name, $onType);
    }

    public function toString(): string
    {
        if (!$this->hasAttributes()) {
            return '';
        }

        return '... on ' . $this->getOnType() . ' {' . PHP_EOL
            . implode(PHP_EOL, array_map(
                fn(IsStringableInterface $item) => $item->toString(),
                $this->getAttributes()
            )) . PHP_EOL . '}' . PHP_EOL;
    }

    public function inline(): string
    {
        return $this->toString();
    }
}
