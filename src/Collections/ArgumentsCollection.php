<?php
/**
 * Created by PhpStorm.
 * User: claudiopinto
 * Date: 04/10/2017
 * Time: 15:11
 */

namespace GraphQL\Collections;

use GraphQL\Abstracts\CollectionAbstract;
use GraphQL\Contracts\Entities\ArgumentInterface;
use GraphQL\Contracts\Entities\VariableInterface;
use GraphQL\Contracts\Properties\HasVariablesInterface;
use GraphQL\Entities\Argument;
use GraphQL\Exceptions\InvalidArgumentTypeException;

class ArgumentsCollection extends CollectionAbstract
    implements HasVariablesInterface
{
    public function __construct(array $array)
    {
        foreach ($array as $key => $value) {
            if (!is_object($value)) {
                $array[$key] = new Argument($value, $key);
            }
        }
        $this->elements = $array;
    }

    /**
     * @return VariableInterface[]
     */
    public function getVariables(): array
    {
        return array_filter($this->elements, fn($item) => $item instanceof VariableInterface);
    }

    public function removeVariable(VariableInterface $variable): self
    {
        foreach ($this->elements as $key => $value) {
            if ($value instanceof VariableInterface && $value->toString() === $variable->toString()) {
                unset($this->elements[$key]);
            }
        }

        return $this;
    }

    public function hasVariables(): bool
    {
        return count($this->getVariables()) > 0;
    }

    public function addVariable(VariableInterface $variable): self
    {
        foreach ($this->elements as $key => $value) {
            if ($value instanceof VariableInterface && $value->getName() === $variable->getName()) {
                return $this;
            }
        }

        $this->elements[] = $variable;

        return $this;
    }

    /**
     * @return ArgumentInterface[]
     */
    public function getArguments(): array
    {
        return array_filter($this->elements, fn($item) => $item instanceof ArgumentInterface);
    }

    public function removeArgument(ArgumentInterface $argument): self
    {
        foreach ($this->elements as $key => $value) {
            if ($value instanceof ArgumentInterface && $value->toString() == $argument->toString()) {
                unset($this->elements[$key]);
            }
        }

        return $this;
    }

    public function hasArguments(): bool
    {
        return count($this->getArguments()) > 0;
    }

    public function addArgument(ArgumentInterface $argument): self
    {
        foreach ($this->elements as $element) {
            if ($element instanceof ArgumentInterface && $element->toString() === $argument->toString()) {
                return $this;
            }
        }

        $this->elements[] = $argument;

        return $this;
    }

    public function toString(): string
    {
        return rtrim($this->stringify($this->elements), ', ');
    }

    /**
     * @param array|int|float|string|VariableInterface|ArgumentInterface $input
     *
     * @return string
     * @throws InvalidArgumentTypeException
     */
    private function stringify($input): string
    {
        if (!is_scalar($input) && !is_array($input)
            && !($input instanceof VariableInterface)
            && !($input instanceof ArgumentInterface)
        ) {
            throw new InvalidArgumentTypeException(!is_scalar($input) ? get_class($input) : 'Non Array Scalar');
        }

        if ($input instanceof VariableInterface) {
            return $input->getName() . ': $' . $input->getName();
        }

        if ($input instanceof ArgumentInterface) {
            return $input->toString();
        }

        return implode(
            ' ',
            array_map(fn($loopKey) => $this->stringify($input[$loopKey]), array_keys($input)),
        );
    }
}
