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
use GraphQL\Contracts\Entities\FragmentInterface;
use GraphQL\Contracts\Entities\VariableInterface;
use GraphQL\Contracts\Properties\HasVariablesInterface;
use GraphQL\Entities\Argument;
use GraphQL\Entities\Variable;
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
     * @return Variable[]
     */
    public function getVariables(): array
    {
        return array_filter($this->elements, fn($item) => $item instanceof VariableInterface);
    }

    public function toString(): string
    {
        return rtrim($this->stringify($this->elements), ', ');
    }

    public function removeVariable(VariableInterface $variable): self
    {
        return $this;
    }

    public function hasVariables(): bool
    {
        return count($this->getVariables()) > 0;
    }

    public function addVariable(VariableInterface $variable): self
    {
        $this->elements[] = $variable;

        return $this;
    }

    /**
     * @param array|int|float|string|VariableInterface|ArgumentInterface $input
     * @param string|null                                                $key ;
     *
     * @return string
     * @throws InvalidArgumentTypeException
     */
    private function stringify($input, ?string $key = null): string
    {
        if (!is_scalar($input) && !is_array($input)
            && !($input instanceof VariableInterface)
            && !($input instanceof ArgumentInterface)
        ) {
            throw new InvalidArgumentTypeException(is_object($input) ? get_class($input) : gettype($input));
        }

        if ($input instanceof VariableInterface) {
            return $input->getName() . ': $' . $input->getName();
        }

        if ($input instanceof ArgumentInterface) {
            return $input->toString();
        }

        if (is_array($input)) {
            return implode(
                ' ',
                array_map(fn($loopKey) => $this->stringify($input[$loopKey], $loopKey), array_keys($input)),
            );
        }

        return json_encode($input);
    }
}
