<?php
/**
 * Created by PhpStorm.
 * User: claudiopinto
 * Date: 29/09/2017
 * Time: 15:32
 */

namespace GraphQL\Entities;

use GraphQL\Collections\ArgumentsCollection;
use GraphQL\Contracts\Entities\AliasInterface;
use GraphQL\Contracts\Entities\FragmentInterface;
use GraphQL\Contracts\Entities\NodeInterface;
use GraphQL\Contracts\Entities\RootNodeInterface;
use GraphQL\Contracts\Entities\VariableInterface;
use GraphQL\Contracts\Properties\HasFragmentsInterface;
use GraphQL\Exceptions\InvalidArgumentTypeException;
use GraphQL\Traits\HasFragmentsTrait;
use ReflectionClass;

/**
 * Class Node
 * @method self use (...$properties)
 *
 * @package GraphQL
 */
class OldNode implements NodeInterface
{
    use HasFragmentsTrait;

    /**
     * @var FragmentInterface[]
     */
    private array $fragments = [];

    /**
     * @var VariableInterface[]
     */
    protected array $variables = [];

    /**
     * @var NodeInterface[]
     */
    private array $modules = [];

    private array $properties = [];

    private RootNodeInterface $root;

    private Alias $keyName;

    private string $baseName;

    private ?NodeInterface $parentNode = null;

    public function __construct(string $name = null, array $properties = null)
    {
        if ($name) {
            $this->setKeyName($this->buildKeyName($name, $properties));
            $this->setBaseName($name);
        }
    }

    public function __get($name): NodeInterface
    {
        return $this->buildNode($name);
    }

    public function __set(string $name, NodeInterface $value)
    {
        $this->modules[$name] = $value;
    }

    public function __call(string $name, array $arguments): NodeInterface
    {
        switch ($name) {
            case "use":
                return $this->get(...$arguments);
            default :
                return $this->buildNode($name, $arguments);
        }
    }

    public function __toString(): string
    {
        return $this->query();
    }

    public function getBaseName(): string
    {
        return $this->baseName;
    }

    public function setBaseName(string $baseName): self
    {
        $this->baseName = $baseName;

        return $this;
    }

    /**
     * @return FragmentInterface[]
     */
    public function getFragments(): array
    {
        return $this->fragments;
    }

    public function addFragment(FragmentInterface $fragment): self
    {
        $this->fragments[$fragment->getKeyName()->getKey()] = $fragment;

        return $this;
    }

    public function removeFragment(FragmentInterface $fragment): self
    {
        unset($this->fragments[$fragment->getKeyName()->getKey()]);

        return $this;
    }

    public function getKeyName(): AliasInterface
    {
        return $this->keyName;
    }

    public function setKeyName(string $keyName): self
    {
        try {
            //:TODO why I need reflection
            $this->keyName = new Alias($keyName ?? (new ReflectionClass($this))->getShortName());
        } catch (\ReflectionException $e) {
            $this->keyName = new Alias('catch');
        }

        return $this;
    }

    public function getParentNode(): ?NodeInterface
    {
        return $this->parentNode;
    }

    public function setParentNode(NodeInterface $parentNode): self
    {
        $this->parentNode = $parentNode;

        return $this;
    }

    public function prev(): NodeInterface
    {
        return $this->getParentNode();
    }

    public function root(): RootNodeInterface
    {
        return $this->getRootNode();
    }

    /**
     * @return Node
     */
    public function clear(): self
    {
        $this->modules = [];
        $this->properties = [];

        return $this;
    }

    public function alias(string $alias, ?string $who = null): self
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
    public function toQL(int $index = 0, $prettify = true): string
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
    public function query(int $index = 0, $prettify = true): string
    {
        $tab = $prettify ? self::TAB : 0;
        $crl = $prettify ? PHP_EOL : '';
        $string = str_repeat(" ", $index * $tab)
            . "{" . $crl . str_repeat(" ", ($index + 1) * $tab)
            . "{$this->getKeyName()} {$this->toQl($index + 2, $prettify)}"
            . "}";

        return $string;
    }

    public function removeVariable(VariableInterface $variable): self
    {
        unset($this->variables[$variable->getName()]);

        return $this;
    }

    public function hasVariables(): bool
    {
        return count($this->variables);
    }

    /**
     * @throws InvalidArgumentTypeException
     */
    protected function get(): self
    {
        $args = func_get_args();
        foreach ($args as $arg) {
            if ($arg instanceof Fragment) {
                $arg = $this->handleFragment($arg);
            }

            if ($arg instanceof Variable) {
                throw new InvalidArgumentTypeException(Variable::class);
            }

            $alias = new Alias($arg);
            $this->properties[$alias->getKey()] = $alias;
        }

        return $this;
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
        $builder = new ArgumentsCollection($arguments);

        $query = $builder->convert();

        foreach ($builder->getVariables() as $variable) {
            $this->getRootNode()->addVariable($variable);
        }

        return $query;
    }

    protected function buildNode($name, $arguments = null): NodeInterface
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

    protected function getRootNode(): ?RootNodeInterface
    {
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
