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
     * @param $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        $className = __NAMESPACE__ . '\\Entities\\' . ucfirst($name);
        if (class_exists($className)) {
            $this->modules[$name] = (new $className())->setKeyName($name);
        } else {
            $this->modules[$name] = (new Graph())->setKeyName($name);
        }

        return $this->modules[$name];
    }

    public function __set($name, $value)
    {
        $this->properties[$name] = $value;
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
                $className = __NAMESPACE__ . '\\Entities\\' . ucfirst($name);
                $args = " ";
                if (isset($arguments[0])) {
                    foreach ($arguments[0] as $key => $value) {
                        $args .= "{$key}: {$value} ";
                    }
                }
                $keyName = "{$name}({$args})";
                if (class_exists($className)) {
                    $this->modules[$keyName] = (new $className())->setKeyName($keyName);
                } else {
                    $this->modules[$keyName] = (new Graph())->setKeyName($keyName);
                }

                return $this->modules[$keyName];
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
    public function toQL(): string
    {
        $ql = "{";
        foreach ($this->properties as $property) {
            $ql .= " {$property} ";
        }

        /** @var Graph $module */
        foreach ($this->modules as $module) {
            $ql .= " {$module->getKeyName()} " . $module->toQL();
        }
        return $ql . "} ";
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
    public function query(): string
    {
        $string = "{ {$this->getKeyName()} {$this->toQl()} }";
        return preg_replace('/\s{2,}/', ' ', $string);
    }

}