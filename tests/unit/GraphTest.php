<?php

namespace tests\unit;


use GraphQL\Alias;
use GraphQL\Graph;
use PHPUnit\Framework\TestCase;

class GraphTest extends TestCase
{
    public function testGetSetKeyName()
    {
        $graph = new Graph('hero', ['id' => '1']);
        $this->assertInstanceOf(Graph::class, $graph);
        $this->assertInstanceOf(Alias::class, $graph->getKeyName());
        $this->assertEquals('hero(id: "1")', (string)$graph->getKeyName());

        return $graph;
    }

    /**
     * @param Graph $parent
     *
     * @depends testGetSetKeyName
     * @return Graph
     */
    public function testGetSetParentNode(Graph $parent)
    {
        $node = new Graph('costumes');
        $node->setParentNode($parent);
        $this->assertInstanceOf(Graph::class, $node->getParentNode());

        return $parent;
    }

    /**
     * @param Graph $graph
     *
     * @depends testGetSetParentNode
     *
     * @return Graph
     */
    public function testGet(Graph $graph)
    {
        $graph->use('name', 'id');

        $this->assertEquals('{hero(id: "1") {name id}}', $graph->query(0, false));

        return $graph;
    }

    /**
     * @param Graph $graph
     *
     * @depends testGet
     *
     * @return Graph
     */
    public function testOn(Graph $graph)
    {
        $node = $graph->on('FooBar')->use('bar_foo');
        $this->assertInstanceOf(Graph::class, $node);
        $this->assertEquals('... on FooBar', (string)$node->getKeyName());

        return $graph;
    }

    /**
     * @param Graph $graph
     *
     * @depends testOn
     *
     * @return Graph
     */
    public function testAlias(Graph $graph)
    {
        $graph->alias('bar');
        $this->assertEquals('{bar: hero(id: "1") {name id ... on FooBar {bar_foo}}}', $graph->query(0, false));

        return $graph;
    }

    /**
     * @param Graph $graph
     *
     * @depends testAlias
     *
     * @return Graph
     */
    public function testToArray(Graph $graph)
    {
        $this->assertIsArray($graph->toArray());

        return $graph;
    }

    /**
     * @param Graph $graph
     *
     * @depends testToArray
     */
    public function testQuery(Graph $graph)
    {
        $this->assertIsString($graph->query());
        $this->assertEquals('{bar: hero(id: "1") {name id ... on FooBar {bar_foo}}}', $graph->query(0, false));
    }
}