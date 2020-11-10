<?php
/**
 * Created by PhpStorm.
 * User: claudiopinto
 * Date: 04/10/2017
 * Time: 15:11
 */

namespace GraphQL;

/**
 * Class Arguments
 * An helper to convert Arrays into GraphQL properties
 *
 * @package GraphQL
 */
class Arguments
{
    /**
     * @var Variable[]
     */
    private $variables;

    /**
     * @var array
     */
    private $array;

    /**
     * ArrayToGraphQL constructor.
     *
     * @param array $array
     */
    public function __construct(array $array)
    {
        $this->array = $array;
        $this->variables = [];
    }

    /**
     * @return string
     */
    public function convert(): string
    {
        return $this->parse($this->array);
    }

    /**
     * @param mixed $input
     *
     * @return string
     */
    protected function parse($input): string
    {
        if (!is_array($input)) {
            return json_encode($input);
        }

        $parsed = "";
        foreach ($input as $key => $value) {
            /** @var Variable $value */
            if ($value instanceof Variable) {
                $parsed .= (is_numeric($key) ? $value->getName() : $key) . ': $' . $value->getName() . ', ';
                $this->variables[$value->getName()] = $value;
            } else {
                $key = (!is_numeric($key) ? ($key . ": ") : '');
                if (!is_array($value)) {
                    $parsed .= $key . $this->parse($value) . ', ';
                } elseif (is_null(key($value)) || is_numeric(key($value))) {
                    $parsed .= $key . "[" . $this->parse($value) . "], ";
                } else {
                    $parsed .= $key . "{" . $this->parse($value) . "}, ";
                }
            }
        }

        return rtrim($parsed, ', ');
    }

    /**
     * @return Variable[]
     */
    public function getVariables(): array
    {
        return $this->variables;
    }
}