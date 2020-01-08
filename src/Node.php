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
 *
 * @package GraphQL
 */
class Node
{
    const TAB = 2;
    /**
     * @var array|Node[]
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
     * @var Node
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
     *
     * @return Node
     */
    public function setKeyName(string $keyName): Node
    {
        try {
            $this->keyName = new Alias($keyName ?? (new ReflectionClass($this))->getShortName());
        } catch (\ReflectionException $e) {
            $this->keyName = new Alias('catch');
        }

        return $this;
    }

    /**
     * @return Node|null
     */
    public function getParentNode(): ?Node
    {
        return $this->parentNode;
    }

    /**
     * @param Node $parentNode
     *
     * @return $this
     */
    public function setParentNode(Node $parentNode): Node
    {
        $this->parentNode = $parentNode;

        return $this;
    }

    /**
     * Graph constructor.
     *
     * @param null $name
     * @param null $properties
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
     * @param Node  $value
     */
    public function __set($name, Node $value)
    {
        $this->modules[$name] = $value;
    }

    /**
     * @param $name
     * @param $arguments
     *
     * @return Node
     */
    public function __call($name, $arguments): Node
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
    public function get(): Node
    {
        $args = func_get_args();
        foreach ($args as $arg) {
            if ($arg instanceof Fragment) {
                /** @var Graph $parent */
                $current = $this;
                $parent = null;
                while ($parent = $current->getParentNode()) {
                    $current = $parent;
                }

                if ($current instanceof Graph) {
                    $current->addFragment($arg);
                }
                
                $arg = '...' . $arg->getKeyName();
            }

            $alias = new Alias($arg);
            $this->properties[$alias->getKey()] = $alias;
        }

        return $this;
    }

    /**
     * @param string $object
     *
     * @return Node
     */
    public function on(string $object): Node
    {
        $key = "... on {$object}";

        return ($this->modules[$key] = (new Node())->setKeyName($key));
    }

    /**
     * @return Node
     */
    public function prev(): Node
    {
        return $this->getParentNode();
    }

    /**
     * @return Node
     */
    public function clear(): Node
    {
        $this->modules = [];
        $this->properties = [];

        return $this;
    }

    /**
     * @param      $alias
     * @param null $who
     *
     * @return Node
     */
    public function alias($alias, $who = null): Node
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
        /** @var Node $module */
        foreach ($this->modules as $module) {
            $array[(string)$module->getKeyName()] = $module->toArray();
        }

        return array_merge($array, $this->properties);
    }

    /**
     * @param      $index
     * @param bool $prettify
     *
     * @return string
     * @todo this method looks weird needs refactoring
     */
    public function toQL($index, $prettify = true): string
    {
        $tab = $prettify ? self::TAB : 0;
        $crl = $prettify ? PHP_EOL : '';
        $glue = $prettify ? '' : ' ';
        $props = [];
        $mods = [];

        $ql = "{" . $crl;
        foreach ($this->properties as $property) {
            $props[] = preg_replace("/\n{2,}/i", "\n", str_repeat(' ', $index * $tab) . "{$property}" . $crl);
        }

        /** @var Node $module */
        foreach ($this->modules as $module) {
            $mods[] = preg_replace("/\n{2,}/i", "\n", str_repeat(' ', $index * $tab) . "{$module->getKeyName()} " . $module->toQL($index + 1, $prettify) . $crl);
        }

        $ql .= implode($glue, array_merge($props, $mods));

        return $ql . str_repeat(' ', ($index * $tab) - $tab) . "}" . $crl;
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return $this->__toString();
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
        $crl = $prettify ? PHP_EOL : '';
        $string = str_repeat(" ", $index * $tab)
            . "{" . $crl . str_repeat(" ", ($index + 1) * $tab)
            . "{$this->getKeyName()} {$this->toQl($index + 2, $prettify)}"
            . "}";

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

    protected function buildNode($name, $arguments = null): Node
    {
        $className = __NAMESPACE__ . '\\Entities\\' . ucfirst($name);

        $keyName = $this->buildKeyName($name, $arguments[0] ?? null);

        if (class_exists($className)) {
            $this->modules[$keyName] = (new $className())->setKeyName($keyName)->setParentNode($this);
        } else {
            $this->modules[$keyName] = (new Node())->setKeyName($keyName)->setParentNode($this);
        }

        return $this->modules[$keyName];
    }

}
