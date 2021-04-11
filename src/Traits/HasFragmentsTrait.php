<?php


namespace GraphQL\Traits;


use GraphQL\Contracts\Entities\FragmentInterface;
use GraphQL\Contracts\Entities\InlineFragmentInterface;
use GraphQL\Entities\InlineFragment;
use GraphQL\Contracts\Properties\HasFragmentsInterface;

/**
 * Trait HasFragmentsTrait
 *
 * @implements HasFragmentsInterface
 *
 * @package GraphQL\Traits
 */
trait HasFragmentsTrait
{
    protected array $fragments = [];

    final public function addFragment(FragmentInterface $fragment): HasFragmentsInterface
    {
        $this->fragments[] = $fragment;

        return $this;
    }

    final public function removeFragment(FragmentInterface $fragment): HasFragmentsInterface
    {
        $match = array_filter($this->fragments, fn(FragmentInterface $item) => $item->inline() === $fragment->inline());

        if (count($match) > 0) {
            unset($this->fragments[key($match)]);
        }

        return $this;
    }

    final public function hasFragments(): bool
    {
        return count($this->fragments) > 0;
    }

    final public function getFragments(): array
    {
        return $this->fragments;
    }
}
