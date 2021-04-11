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
trait HasInlineFragmentsTrait
{
    protected array $fragments = [];

    final public function on($inlineFragment): InlineFragmentInterface
    {
        if (!($inlineFragment instanceof InlineFragmentInterface)) {
            $inlineFragment = new InlineFragment($inlineFragment);
        }

        $inlineFragment->setParentNode($this);

        $this->fragments[] = $inlineFragment;

        return $inlineFragment;
    }
}
