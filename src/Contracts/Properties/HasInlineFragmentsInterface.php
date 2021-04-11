<?php


namespace GraphQL\Contracts\Properties;


use GraphQL\Contracts\Entities\InlineFragmentInterface;

interface HasInlineFragmentsInterface
{
    /**
     * @param string|InlineFragmentInterface $inlineFragment
     *
     * @return InlineFragmentInterface
     */
    public function on($inlineFragment): InlineFragmentInterface;
}
