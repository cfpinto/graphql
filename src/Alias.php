<?php


namespace GraphQL;


class Alias
{
    /**
     * @var string
     */
    private $alias;

    /**
     * @var string
     */
    private $key;

    /**
     * Alias constructor.
     * @param $key
     * @param $alias
     */
    public function __construct($key, $alias = null)
    {
        $this->setKey($key);
        $this->setAlias($alias);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getAlias() ?
            $this->getAlias() . ': ' . $this->getKey() :
            $this->getKey();
    }

    /**
     * @return string|null
     */
    public function getAlias(): ?string
    {
        return $this->alias;
    }

    /**
     * @param string $alias
     */
    public function setAlias(string $alias = null)
    {
        $this->alias = $alias;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @param string $key
     */
    public function setKey(string $key)
    {
        $this->key = $key;
    }
}
