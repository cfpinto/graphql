<?php
namespace tests\unit;

use GraphQL\ArrayToGraphQL;
use PHPUnit\Framework\TestCase;

final class ArrayToGraphQLTest extends TestCase
{
    /**
     * @dataProvider convertData
     */
    public function testConvert($data, $assertion)
    {
        $arrayToGraphQL = new ArrayToGraphQL($data);
        $this->assertIsString($arrayToGraphQL->convert());
        $this->assertEquals($assertion, $arrayToGraphQL->convert());
    }

    public function convertData()
    {
        return [
            [["foo"], '"foo"'],
            [["foo" => "bar"], 'foo: "bar"'],
            [["foo" => ["bar"]], 'foo: ["bar"]'],
            [["foo" => ["try" => "bar"]], 'foo: {try: "bar"}'],
            [[['foo'=> 'bar']], '{foo: "bar"}']
        ];
    }
}