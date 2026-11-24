<?php


namespace GraphQL\Contracts\Properties;


interface HasOperationNameInterface
{
    public function getOperationName(): ?string;

    public function setOperationName(?string $operationName): static;

    public function hasOperationName(): bool;
}
