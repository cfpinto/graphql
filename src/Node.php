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
 * Class Node
 * @method self use (...$properties)
 *
 * @package GraphQL
 */
class Node
{
    const TAB = 2;

    /**
     * @var Fragment[]
     */
    private $fragments = [];

    /**
     * @var Variable[]
     */
    protected $variables = [];

    /**
     * @var array|Node[]
     */
    private $modules = [];

    /**
     * @var array
     */
    private $properties = [];

    /**
     * @var Node
     */
    private $root;

    /**
     * @var Alias
     */
    private $keyName;

    /**
     * @var string
     */
    private $baseName;

    /**
     * @return string
     */
    public function getBaseName(): string
    {
        return $this->baseName;
    }

    /**
     * @var Node
     */
    private $parentNode;

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
     * Node constructor.
     *
     * @param null $name
     * @param null $properties
     */
    public function __construct($name = null, $properties = null)
    {
        if ($name) {
            $this->setKeyName($this->buildKeyName($name, $properties));
            $this->baseName = $name;
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
     * @return Node
     * @throws Exception
     */
    public function get(): Node
    {
        $args = func_get_args();
        foreach ($args as $arg) {
            if ($arg instanceof Fragment) {
                $arg = $this->handleFragment($arg);
            }

            if ($arg instanceof Variable) {
                throw new Exception('Invalid argument type Variable');
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
        $onNode = new Node();
        $onNode->setParentNode($this)
            ->setKeyName($key);

        return ($this->modules[$key] = $onNode);
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
    public function root(): Node
    {
        return $this->getRootNode();
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

    /**
     * @param string $name
     * @param mixed  $arguments
     *
     * @return string
     */
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
        $builder = new Arguments($arguments);

        $query = $builder->convert();

        foreach ($builder->getVariables() as $variable) {
            $this->getRootNode()->addVariable($variable);
        }

        return $query;
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

    protected function handleVariable(Variable $variable): string
    {
        if (($current = $this->getRootNode())) {
            $current->addVariable($variable);
        }

        return '$' . $variable->getName();
    }

    protected function addVariable(Variable $variable): self
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

    /**
     * @param Fragment $fragment
     *
     * @return string
     */
    protected function handleFragment(Fragment $fragment): string
    {
        if (($current = $this->getRootNode())) {
            $current->addFragment($fragment);
        }

        return '...' . $fragment->getKeyName();
    }

    /**
     * @return Node
     */
    protected function getRootNode(): ?Node
    {
        /** @var Node $parent */
        if (!empty($this->root)) {
            return $this->root;
        }

        $current = $this;
        $parent = null;
        while ($parent = $current->getParentNode()) {
            $current = $parent;
        }

        return ($this->root = $current);
    }

}
