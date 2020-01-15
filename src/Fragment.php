<?php


namespace GraphQL;


class Fragment extends Node
{

    /**
     * @var string|null
     */
    private $on;

    public function __construct($name = null, $on = null)
    {
        $this->on = $on;
        parent::__construct($name, null);
    }

    /**
     * @param int  $index
     * @param bool $prettify
     *
     * @return string
     */
    public function query($index = 0, $prettify = true): string
    {
        $tab = $prettify ? self::TAB : 0;

        return str_repeat(" ", $index * $tab)
            . "fragment " . $this->getKeyName() . " on " . $this->on . ' '
            . "{$this->toQl($index + 1, $prettify)}";
    }
}