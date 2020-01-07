<?php
/**
 * Created by PhpStorm.
 * User: claudiopinto
 * Date: 29/09/2017
 * Time: 15:32
 */

namespace GraphQL;

use ReflectionClass;
/**
 * Class Graph
 * @method self use (...$properties)
 * @package GraphQL
 */
class Graph
{
    const TAB = 2;
    /**
     * @var array|Graph[]
     */
    private $modules = [];

    /**
     * @var array
     */
    private $properties = [];

    /**
     * @var Alias
     */
    private $keyName;

    /**
     * @var Graph
     */
    private $parentNode;

    /**
     * @return Alias
     */
    public function getKeyName(): Alias
    {
        return $this->keyName;
    }

    /**
     * @param string $keyName
     * @return Graph
     * @throws \ReflectionException
     */
    public function setKeyName(string $keyName): Graph
    {
        $this->keyName = new Alias($keyName ?? (new ReflectionClass($this))->getShortName());
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
     * @param null $name
     * @param null $properties
     * @throws \ReflectionException
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
        foreach ($args as $arg) {
            $alias = new Alias($arg);
            $this->properties[$alias->getKey()] = $alias;
        }

        return $this;
    }

    /**
     * @param string $object
     * @return Graph
     * @throws \ReflectionException
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
     * @param $alias
     * @param null $who
     * @return Graph
     */
    public function alias($alias, $who = null): Graph
    {
        if ($who && isset($this->modules[$who])) {
            $this->modules[$who]->getKeyName()->setAlias($alias);
        } elseif (!$who) {
            $this->getKeyName()->setAlias($alias);
        }

        if ($who && isset($this->properties[$who])) {
            $this->properties[$who]->setAlias($alias);
        }

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
            $array[(string)$module->getKeyName()] = $module->toArray();
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
     * @param int $index
     * @param bool $prettify
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
