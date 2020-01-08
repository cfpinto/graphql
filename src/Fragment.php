<?php


namespace GraphQL;


class Fragment extends Node
{

    /**
     * @var string|null
     */
    private $target;

    public function __construct($name = null, $target = null)
    {
        $this->target = $target;
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
        $string = str_repeat(" ", $index * $tab)
            . "fragment " . $this->getKeyName() . " on " . $this->target . ' '
            . "{$this->toQl($index + 1, $prettify)}";

        return $string;
    }
}