<?php


namespace GraphQL\Entities;


use GraphQL\Contracts\Entities\ArgumentInterface;
use GraphQL\Traits\IsStringableTrait;

class Argument implements ArgumentInterface
{
    use IsStringableTrait;

    protected ?string $key;

    /**
     * @var mixed
     */
    protected $value;

    public function __construct($value, ?string $key = null)
    {
        $this->key = $key;
        $this->value = $value;
    }

    public function toString(): string
    {
        if (is_numeric($this->key) || empty($this->key)) {
            if (is_string($this->value)) {
                return $this->value;
            }

            return json_encode($this->value);
        }

        if (is_string($this->value)) {
            return $this->key . ': ' . $this->value;
        }

        return $this->key . ': ' . json_encode($this->value);
    }
}
