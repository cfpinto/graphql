<?php
/**
 * Created by PhpStorm.
 * User: claudiopinto
 * Date: 29/09/2017
 * Time: 15:32
 */

namespace GraphQL;

class Graph
{
    const TAB = 2;
    /**
     * @var array
     */
    private $modules = [];

    /**
     * @var array
     */
    private $properties = [];

    /**
     * @var string
     */
    private $keyName;

    /**
     * @var Graph
     */
    private $parentNode;

    /**
     * @return string
     */
    public function getKeyName(): string
    {
        return $this->keyName ?? strtolower(class_basename($this));
    }

    /**
     * @param string $keyName
     *
     * @return Graph
     */
    public function setKeyName(string $keyName): Graph
    {
        $this->keyName = $keyName;
        return $this;
    }

    /**
     * @return Graph
     */
    public function getParentNode(): Graph
    {
        return $this->parentNode;
    }

    /**
     * @param Graph $parentNode
     *
     * @return $this
     */
    public function setParentNode(Graph $parentNode): Graph
    {
        $this->parentNode = $parentNode;
        return $this;
    }

    /**
     * Graph constructor.
     *
     * @param $name
     * @param $properties
     */
    public function __construct($name = null, $properties = null)
    {
        if ($name) {
            $this->setKeyName($this->buildKeyName($name, $properties));
        }
    }

    /**
     * @param $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        return $this->buildNode($name);
    }

    /**
     * @param       $name
     * @param Graph $value
     */
    public function __set($name, Graph $value)
    {
        $this->modules[$name] = $value;
    }

    /**
     * @param $name
     * @param $arguments
     *
     * @return Graph
     * @throws Exception
     */
    public function __call($name, $arguments): Graph
    {
        switch ($name) {
            case "use":
                return call_user_func_array([$this, 'get'], $arguments);
            default :
                return $this->buildNode($name, $arguments);
        }

        throw new Exception("method {$name} not found");
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->query();
    }

    /**
     * @return $this
     */
    public function get(): Graph
    {
        $args = func_get_args();
        $this->properties = array_merge($this->properties, $args);
        return $this;
    }

    /**
     * @param string $object
     *
     * @return Graph
     */
    public function on(string $object): Graph
    {
        $key = "... on {$object}";
        return ($this->modules[$key] = (new Graph())->setKeyName($key));
    }

    /**
     * @return Graph
     */
    public function prev(): Graph
    {
        return $this->getParentNode();
    }

    /**
     * @return Graph
     */
    public function clear(): Graph
    {
        $this->modules = [];
        $this->properties = [];
        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $array = [];
        /** @var Graph $module */
        foreach ($this->modules as $module) {
            $array[$module->getKeyName()] = $module->toArray();
        }

        return array_merge($array, $this->properties);
    }

    /**
     * @return string
     */
    public function toQL($index): string
    {
        $ql = "{\n";
        foreach ($this->properties as $property) {
            $ql .= str_repeat(' ', $index * self::TAB) . "{$property}\n";
        }

        /** @var Graph $module */
        foreach ($this->modules as $module) {
            $ql .= str_repeat(' ', $index * self::TAB) . "{$module->getKeyName()} " . $module->toQL($index + 1);
        }
        return $ql . str_repeat(' ', ($index * self::TAB) - self::TAB) . "}\n";
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return $this->__toString();
    }

    /**
     * @return string
     */
    public function query($index = 0, $prettify = true): string
    {
        $string = str_repeat(" ", $index * self::TAB)
            . "{\n" . str_repeat(" ", ($index + 1) * self::TAB)
            . "{$this->getKeyName()} {$this->toQl($index + 2)}"
            . "}";

        if (!$prettify) {
            return preg_replace(['/\n/', '/\s{2,}/i'], ['  ', '  '], $string);
        }

        return $string;
    }

    protected function buildKeyName($name, $arguments = null): string
    {
        $keyName = $name;

        if (!empty($arguments)) {
            $args = $this->buildArgs($arguments);
            $keyName .= "({$args})";
        }

        return $keyName;
    }

    protected function buildArgs($arguments)
    {
        $builder = new ArrayToGraphQL($arguments);

        return $builder->convert();
    }

    protected function buildNode($name, $arguments = null): Graph
    {
        $className = __NAMESPACE__ . '\\Entities\\' . ucfirst($name);

        $keyName = $this->buildKeyName($name, $arguments[0] ?? null);

        if (class_exists($className)) {
            $this->modules[$keyName] = (new $className())->setKeyName($keyName)->setParentNode($this);
        } else {
            $this->modules[$keyName] = (new Graph())->setKeyName($keyName)->setParentNode($this);
        }

        return $this->modules[$keyName];
    }

}