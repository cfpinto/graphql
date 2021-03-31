<?php


namespace GraphQL\Contracts\Properties;


use GraphQL\Contracts\Entities\FragmentInterface;

interface HasFragmentsInterface
{
    public function addFragment(FragmentInterface $fragment): HasFragmentsInterface;

    public function removeFragment(FragmentInterface $fragment): HasFragmentsInterface;

    public function hasFragments(): bool;

    /**
     * @return FragmentInterface[];
     */
    public function getFragments(): array;

}
