<?php
/**
 * Created by PhpStorm.
 * User: claudiopinto
 * Date: 04/10/2017
 * Time: 14:48
 */

namespace GraphQL;

/**
 * Class Mutation
 * @package GraphQL
 */
class Mutation extends Node
{

    /**
     * Mutation constructor.
     *
     * @param null $mutation
     * @param null $properties
     */
    public function __construct($mutation, $properties)
    {
        parent::__construct($mutation, $properties);
    }

    /**
     * @param int  $index
     * @param bool $prettify
     *
     * @return string
     */
    public function query($index = 0, $prettify = true): string
    {
        if ($this->hasVariables()) {
            $args = [];

            foreach ($this->variables as $variable) {
                $args[] = $variable->parse();
            }

            return sprintf("mutation %sMutation(%s)", ucfirst($this->getBaseName()), implode(', ', $args)) .
                 trim(parent::query($index, $prettify));
        }

        return "mutation " . parent::query($index, $prettify);
    }

}