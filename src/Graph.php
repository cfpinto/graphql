<?php


namespace GraphQL;


class Graph extends Node
{
    /**
     * @var Fragment[]
     */
    private $fragments = [];

    /**
     * @var Variable[]
     */
    private $variables = [];

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
        $this->fragments[$fragment->getKeyName()->getKey()] = $fragment;

        return $this;
    }

    /**
     * @param Fragment $fragment
     *
     * @return $this
     */
    public function removeFragment(Fragment $fragment): self
    {
        unset($this->fragments[$fragment->getKeyName()->getKey()]);

        return $this;
    }

    public function addVariable(Variable $variable): self
    {
        $this->variables[$variable->getName()] = $variable;
        
        return $this;
    }

    public function removeVariable(Variable $variable): self
    {
        unset($this->variables[$variable->getName()]);
        
        return $this;
    }

    public function hasVariables(): bool
    {
        return count($this->variables);
    }

    public function query($index = 0, $prettify = true): string
    {
        $crl = $prettify ? PHP_EOL : ' ';
        if ($this->hasVariables()) {
            $name = new \ReflectionClass($this);

            $args = [];
            foreach ($this->variables as $variable) {
                $args[] = $variable->parse();
            }
            
            return 'query get' . $name->getShortName() . '('.implode(', ', $args).')' . trim(parent::query($index, $prettify)) . $crl . implode($crl, $this->fragments);
        }

        return parent::query($index, $prettify);
    }

}