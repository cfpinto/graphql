<?php


namespace GraphQL\Traits;


use InvalidArgumentException;

trait HasOperationNameTrait
{
    protected ?string $operationName = null;

    final public function getOperationName(): ?string
    {
        return $this->operationName;
    }

    /**
     * Sets the name of the GraphQL operation, e.g. `query getMyHeroName { ... }`.
     * Pass null to go back to the generated name.
     *
     * @throws InvalidArgumentException when the name is not a valid GraphQL name
     */
    final public function setOperationName(?string $operationName): static
    {
        if ($operationName !== null && !preg_match('/^[_A-Za-z][_0-9A-Za-z]*$/', $operationName)) {
            throw new InvalidArgumentException("Invalid operation name {$operationName}");
        }

        $this->operationName = $operationName;

        return $this;
    }

    final public function hasOperationName(): bool
    {
        return $this->operationName !== null;
    }
}
