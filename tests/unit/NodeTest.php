<?php

namespace tests\unit;


use GraphQL\Alias;
use GraphQL\Node;
use PHPUnit\Framework\TestCase;

class NodeTest extends TestCase
{
    public function testGetSetKeyName()
    {
        $graph = new Node('hero', ['id' => '1']);
        $this->assertInstanceOf(Node::class, $graph);
        $this->assertInstanceOf(Alias::class, $graph->getKeyName());
        $this->assertEquals('hero(id: "1")', (string)$graph->getKeyName());

        return $graph;
    }

    /**
     * @param Node $parent
     *
     * @depends testGetSetKeyName
     * @return Node
     */
    public function testGetSetParentNode(Node $parent)
    {
        $node = new Node('costumes');
        $node->setParentNode($parent);
        $this->assertInstanceOf(Node::class, $node->getParentNode());

        return $parent;
    }

    /**
     * @param Node $graph
     *
     * @depends testGetSetParentNode
     *
     * @return Node
     */
    public function testGet(Node $graph)
    {
        $graph->use('name', 'id');

        $this->assertEquals('{hero(id: "1") {name id}}', $graph->query(0, false));

        return $graph;
    }

    /**
     * @param Node $graph
     *
     * @depends testGet
     *
     * @return Node
     */
    public function testOn(Node $graph)
    {
        $node = $graph->on('FooBar')->use('bar_foo');
        $this->assertInstanceOf(Node::class, $node);
        $this->assertEquals('... on FooBar', (string)$node->getKeyName());

        return $graph;
    }

    /**
     * @param Node $graph
     *
     * @depends testOn
     *
     * @return Node
     */
    public function testAlias(Node $graph)
    {
        $graph->alias('bar');
        $this->assertEquals('{bar: hero(id: "1") {name id ... on FooBar {bar_foo}}}', $graph->query(0, false));

        return $graph;
    }

    /**
     * @param Node $graph
     *
     * @depends testAlias
     *
     * @return Node
     */
    public function testToArray(Node $graph)
    {
        $this->assertIsArray($graph->toArray());

        return $graph;
    }

    /**
     * @param Node $graph
     *
     * @depends testToArray
     */
    public function testQuery(Node $graph)
    {
        $this->assertIsString($graph->query());
        $this->assertEquals('{bar: hero(id: "1") {name id ... on FooBar {bar_foo}}}', $graph->query(0, false));
    }
}