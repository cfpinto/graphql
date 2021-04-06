<?php


namespace GraphQL\Entities;

use GraphQL\Contracts\Entities\FragmentInterface;
use GraphQL\Contracts\Entities\InlineFragmentInterface;
use GraphQL\Contracts\Entities\VariableInterface;
use GraphQL\Contracts\Properties\IsStringableInterface;
use GraphQL\Exceptions\InvalidArgumentTypeException;
use GraphQL\Exceptions\InvalidMethodException;
use GraphQL\Traits\HasAttributesTrait;
use GraphQL\Traits\HasNameTrait;
use GraphQL\Traits\HasParentTrait;
use GraphQL\Traits\IsStringableTrait;

/**
 * Class Fragment
 *
 * @method FragmentInterface use (...$arguments)
 *
 * @package GraphQL\Entities
 */
class Fragment implements FragmentInterface
{
    use HasAttributesTrait;
    use HasNameTrait;
    use HasParentTrait;
    use IsStringableTrait;

    protected string $onType;

    public function __construct(string $name, string $onType)
    {
        $this->setName($name);
        $this->setOnType($onType);
    }

    /**
     * @param string $method
     * @param array  $arguments
     *
     * @return FragmentInterface
     * @throws InvalidMethodException
     */
    public function __call(string $method, array $arguments): FragmentInterface
    {
        if ($method !== 'use') {
            throw new InvalidMethodException("Invalid method {$method}");
        }

        foreach ($arguments as $argument) {
            if ($argument instanceof VariableInterface) {
                throw new InvalidArgumentTypeException(get_class($argument));
            }

            if ($argument instanceof InlineFragmentInterface) {
                throw new InvalidArgumentTypeException(get_class($argument));
            }

            if ($argument instanceof FragmentInterface) {
                throw new InvalidArgumentTypeException(get_class($argument));
            }

            $alias = new Alias($argument);
            $this->attributes[$alias->getKey()] = $alias;
        }

        return $this;
    }

    public function getOnType(): string
    {
        return $this->onType;
    }

    public function setOnType(string $onType): FragmentInterface
    {
        $this->onType = $onType;

        return $this;
    }

    public function inline(): string
    {
        return '...' . $this->getName();
    }

    public function toString(): string
    {
        if (!$this->hasAttributes()) {
            return '';
        }

        return 'fragment ' . $this->getName() . ' on ' . $this->onType . ' {' . PHP_EOL
            . implode(
                PHP_EOL,
                array_map(
                    fn(IsStringableInterface $item) => $item->toString(),
                    $this->getAttributes()
                )
            ) . PHP_EOL . '}' . PHP_EOL;
    }
}
