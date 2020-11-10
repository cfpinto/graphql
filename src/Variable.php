<?php


namespace GraphQL;


class Variable
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $default;

    /**
     * Variable constructor.
     *
     * @param string      $name
     * @param string      $type
     * @param string|null $default
     */
    public function __construct(string $name, string $type, string $default = '')
    {
        $this->setName($name);
        $this->setType($type);
        $this->setDefault($default);
    }

    public function __toString(): string
    {
        return $this->parse();
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getDefault(): string
    {
        return $this->default;
    }

    /**
     * @param string $default
     */
    public function setDefault(string $default): void
    {
        $this->default = $default;
    }

    /**
     * @return string
     */
    public function parse(): string
    {
        return '$' . $this->getName() . ': ' .
            ($this->getType()) .
            ($this->getDefault() ?
                ' = ' .
                ($this->getType() === 'String' ? json_encode($this->getDefault()) : $this->getDefault())
                : '');
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }
}