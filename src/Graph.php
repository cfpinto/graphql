<?php
/**
 * Created by PhpStorm.
 * User: claudiopinto
 * Date: 29/09/2017
 * Time: 15:32
 */

namespace GraphQL;


use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Psr\Http\Message\StreamInterface;

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
    private $url;

    /**
     * @var string
     */
    private $key;

    /**
     * @var string
     */
    private $keyName;

    /**
     * @var Client
     */
    private $driver;

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @param string $key
     *
     * @return Graph
     */
    public function setKey(string $key): Graph
    {
        $this->key = $key;
        return $this;
    }

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
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     *
     * @return $this
     */
    public function setUrl(string $url): Graph
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return ClientInterface
     */
    public function getDriver(): ClientInterface
    {
        return $this->driver;
    }

    /**
     * @param Client $driver
     *
     * @return Graph
     */
    public function setDriver(Client $driver): Graph
    {
        $this->driver = $driver;
        return $this;
    }

    /**
     * Graph constructor.
     *
     * @param string          $url
     * @param string          $key
     * @param ClientInterface $http
     */
    public function __construct(string $url, string $key, ClientInterface $http = null)
    {
        $this->setKey($key)
            ->setUrl($url)
            ->setDriver($http ?? new Client());
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
            $this->modules[$name] = (new $className($this->url, $this->key))->setKeyName($name);
        } else {
            $this->modules[$name] = (new Graph($this->url, $this->key))->setKeyName($name);
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
                foreach ($arguments[0] as $key => $value) {
                    $args .= "{$key}: {$value} ";
                }
                $keyName = "{$name}({$args})";
                if (class_exists($className)) {
                    $this->modules[$keyName] = (new $className($this->url, $this->key))->setKeyName($keyName);
                } else {
                    $this->modules[$keyName] = (new Graph($this->url, $this->key))->setKeyName($keyName);
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
        return $this->toRequest()->getContents();
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
     * @return StreamInterface
     */
    public function toRequest(): StreamInterface
    {
        return $this->driver->get($this->getUrl(), [
            'headers' => [
                'Content-Type' => 'application/graphql',
                'X-Shopify-Storefront-Access-Token' => $this->getKey()
            ],
            'body' => $this->query()
        ])->getBody();
    }

    /**
     * @return mixed
     */
    public function toJsonResponse()
    {
        return \GuzzleHttp\json_decode($this->toString());
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