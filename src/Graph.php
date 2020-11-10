<?php


namespace GraphQL;


class Graph extends Node
{

    public function query($index = 0, $prettify = true): string
    {
        $crl = $prettify ? PHP_EOL : ' ';
        if ($this->hasVariables()) {
            $name = new \ReflectionClass($this);

            $args = [];
            foreach ($this->variables as $variable) {
                $args[] = $variable->parse();
            }

            return 'query get' . $name->getShortName() . '('.implode(', ', $args).')' . trim(parent::query($index, $prettify)) . $crl . implode($crl, $this->getFragments());
        }

        return parent::query($index, $prettify);
    }

}