<?php
/**
 * Created by PhpStorm.
 * User: claudiopinto
 * Date: 04/10/2017
 * Time: 15:11
 */

namespace GraphQL;

/**
 * Class ArrayToGraphQL
 * An helper to convert Arrays into GraphQL properties
 * @package GraphQL
 */
class ArrayToGraphQL
{
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
    }

    /**
     * @return string
     */
    public function convert(): string
    {
        return $this->parse($this->array);
    }

    /**
     * @param $input
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
            $key = (!is_numeric($key) ? ($key . ": " ) : '');
            if (!is_array($value)) {
                $parsed .= $key . $this->parse($value) . ', ';
            } elseif (is_null(key($value)) || is_numeric(key($value))) {
                $parsed .= $key . "[" . $this->parse($value) . "]";
            } else {
                $parsed .= $key . "{" . $this->parse($value) . "}";
            }
        }

        return rtrim($parsed, ', ');
    }
}