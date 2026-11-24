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

    public function getKey(): ?string
    {
        return $this->key;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function toString(): string
    {
        if (is_numeric($this->key) || empty($this->key)) {
            if (is_string($this->value)) {
                return $this->value;
            }

            return $this->valueToString($this->value);
        }

        return $this->key . ': ' . $this->valueToString($this->value);
    }

    /**
     * Converts a PHP value into a GraphQL value literal.
     * Sequential arrays become lists, associative arrays become input objects
     * with unquoted keys, and enum cases become enum values.
     *
     * @param mixed $value
     */
    protected function valueToString($value): string
    {
        if (is_object($value) && enum_exists(get_class($value))) {
            return $value->name;
        }

        if (is_array($value)) {
            if ($value === [] || array_is_list($value)) {
                return '[' . implode(',', array_map([$this, 'valueToString'], $value)) . ']';
            }

            $pairs = [];
            foreach ($value as $key => $item) {
                $pairs[] = $key . ': ' . $this->valueToString($item);
            }

            return '{' . implode(', ', $pairs) . '}';
        }

        return json_encode($value);
    }
}
