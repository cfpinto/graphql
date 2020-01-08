<?php


namespace GraphQL;


class Graph extends Node
{
    /**
     * @var array Fragment[]
     */
    private $fragments = [];

    public function __construct($name = null, $properties = null)
    {
        parent::__construct($name, $properties);
    }

    /**
     * @return array
     */
    public function getFragments(): array
    {
        return $this->fragments;
    }

    /**
     * @param Fragment $fragment
     *
     * @return $this
     */
    public function addFragment(Fragment $fragment): self
    {
        $this->fragments[get_class($fragment)] = $fragment;

        return $this;
    }

    /**
     * @param Fragment $fragment
     *
     * @return $this
     */
    public function removeFragment(Fragment $fragment): self
    {
        unset($this->fragments[get_class($fragment)]);

        return $this;
    }

    public function query($index = 0, $prettify = true, $expanded = false): string
    {
        if ($expanded) {
            $name = new \ReflectionClass($this);

            return 'query get' . $name->getShortName() . ' ' . trim(parent::query($index, $prettify)) . PHP_EOL . implode(PHP_EOL, $this->fragments);
        }

        return parent::query($index, $prettify);
    }

}