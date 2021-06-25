<?php
namespace Hotlink\Brightpearl\Test\unit\Model\Interaction\Order\Export\Helper\Customisation;

/**
 *
 *  @group  order
 *  @group  interaction
 *  @group  customisation
 *
 */

class EvaluateTest extends \PHPUnit\Framework\TestCase
{

    function test_clean()
    {
        $evaluate = $this->getEvaluator();
        $this->assertEquals( 'abc', $evaluate->clean( ' abc  ' ), "' abc  '" );
        $this->assertEquals( '1 23', $evaluate->clean( '   1 23    ' ), "'   1 23    '" );
    }

    function test_is_literal()
    {
        $evaluate = $this->getEvaluator();
        $this->assertTrue( $evaluate->isLiteral( '[abc]' ),       "'[abc]'" );
        $this->assertTrue( $evaluate->isLiteral( '[123]' ),       "'[123]'" );
        $this->assertTrue( $evaluate->isLiteral( '[ab[cx]yx]' ),  "'[ab[cx]yx]'" );
        $this->assertTrue( $evaluate->isLiteral( '   [ abc ] ' ), "'   [ abc ] '" );
        $this->assertTrue( $evaluate->isLiteral( '[]' ),          "'[]'" );
        $this->assertFalse( $evaluate->isLiteral( 'abc' ),        "'abc'" );
        $this->assertFalse( $evaluate->isLiteral( '[abc' ),       "'[abc'" );
        $this->assertFalse( $evaluate->isLiteral( 'abc]' ),       "'abc]'" );
        $this->assertFalse( $evaluate->isLiteral( 'a[b]c' ),      "'a[b]c'" );
        $this->assertFalse( $evaluate->isLiteral( '{abc}' ),      "'{abc}'" );
        $this->assertFalse( $evaluate->isLiteral( '(abc)' ),      "'(abc)'" );
        $this->assertFalse( $evaluate->isLiteral( '   ' ),        "'   '" );
        $this->assertFalse( $evaluate->isLiteral( '' ),           "''" );
    }

    function test_get_literal()
    {
        $evaluate = $this->getEvaluator();
        $this->assertEquals( 'abc', $evaluate->getLiteral( '[abc]' ) );
        $this->assertEquals( '123', $evaluate->getLiteral( '[123]' ) );
        $this->assertEquals( 'ab[cx]yx', $evaluate->getLiteral( '[ab[cx]yx]' ) );
        $this->assertEquals( ' abc ', $evaluate->getLiteral( '   [ abc ] ' ) );
        $this->assertEquals( '', $evaluate->getLiteral( '[]' ) );
        $this->assertEquals( false, $evaluate->getLiteral( 'abc' ) );
        $this->assertEquals( false, $evaluate->getLiteral( '{abc}' ) );
        $this->assertEquals( false, $evaluate->getLiteral( '(abc)' ) );
        $this->assertEquals( false, $evaluate->getLiteral( '' ) );
    }

    function test_evaluate_property_success()
    {
        $evaluate = $this->getEvaluator();
        $object = $this->getNestedObject();
        $this->assertEquals( 'ok1', $evaluate->evaluate( 'getProperty1', $object ) );
        $this->assertEquals( 'ok2', $evaluate->evaluate( 'getInside1.getProperty2', $object ) );
        $this->assertEquals( 'ok3', $evaluate->evaluate( 'getInside1.getInside2.getProperty3', $object ) );
        $result = $evaluate->evaluate( 'getInside1.getArray', $object );
        $this->assertTrue( is_array( $result ) );
        $this->assertEquals( 3, count( $result ) );
    }

    function test_evaluate_empty()
    {
        $evaluate = $this->getEvaluator();
        $object = $this->getNestedObject();
        $this->assertSame( $object, $evaluate->evaluate( '', $object ) );
    }

    /**
     *  @expectedException \Hotlink\Brightpearl\Model\Exception\Customisation\Parser
     */
    function test_evaluate_not_callable_object_string()
    {
        $evaluate = $this->getEvaluator();
        $evaluate->evaluate( 'getProperty1.result', $this->getNestedObject() );
    }

    /**
     *  @expectedException \Hotlink\Brightpearl\Model\Exception\Customisation\Parser
     */
    function test_evaluate_not_callable_object_nested1_sting()
    {
        $evaluate = $this->getEvaluator();
        $evaluate->evaluate( 'getInside1.getProperty2.noway', $this->getNestedObject() );
    }

    /**
     *  @expectedException \Hotlink\Brightpearl\Model\Exception\Customisation\Parser
     */
    function test_evaluate_not_callable_object_nested2_tring()
    {
        $evaluate = $this->getEvaluator();
        $evaluate->evaluate( 'getInside1.getInside2.getProperty3.nope', $this->getNestedObject() );
    }

    /**
     *  @expectedException \Hotlink\Brightpearl\Model\Exception\Customisation\Parser
     */
    function test_evaluate_not_callable_object_nested_array()
    {
        $evaluate = $this->getEvaluator();
        $evaluate->evaluate( 'getInside1.getInside2.getArray.nochance', $this->getNestedObject() );
    }

    /**
     *  @expectedException \Hotlink\Brightpearl\Model\Exception\Customisation\Parser
     */
    function test_evaluate_not_callable_method_missing()
    {
        $evaluate = $this->getEvaluator();
        $evaluate->evaluate( 'oink', $this->getNestedObject() );
    }

    /**
     *  @expectedException \Hotlink\Brightpearl\Model\Exception\Customisation\Parser
     */
    function test_evaluate_not_callable_method_nested1_missing()
    {
        $evaluate = $this->getEvaluator();
        $evaluate->evaluate( 'getInside1.getSomething', $this->getNestedObject() );
    }

    /**
     *  @expectedException \Hotlink\Brightpearl\Model\Exception\Customisation\Parser
     */
    function test_evaluate_not_callable_method_nested2_missing()
    {
        $evaluate = $this->getEvaluator();
        $evaluate->evaluate( 'getInside1.getInside2.getProperty9', $this->getNestedObject() );
    }

    /**
     *  @expectedException \Hotlink\Brightpearl\Model\Exception\Customisation\Parser
     */
    function test_evaluate_not_callable_method_nested3_missing()
    {
        $evaluate = $this->getEvaluator();
        $evaluate->evaluate( 'getInside1.getInside2.getInside3.oinking', $this->getNestedObject() );
    }

    protected function getNestedObject()
    {
        return new Nest1();
    }

    protected function getEvaluator()
    {
        return new \Hotlink\Brightpearl\Model\Interaction\Order\Export\Helper\Customisation\Evaluate();
    }

}

class Nest1
{
    function getInside1()
    {
        return new Nest2();
    }
    function getProperty1()
    {
        return "ok1";
    }
}

class Nest2
{
    function getInside2()
    {
        return new Nest3();
    }
    function getProperty2()
    {
        return "ok2";
    }
    function getArray()
    {
        return [ new Item( 'item1' ),
                 new Item( 'item2' ),
                 new Item( 'item3' ) ];
    }
}

class Nest3
{
    function getProperty3()
    {
        return "ok3";
    }
}

class Item
{
    protected $_id;
    function __construct( $id )
    {
        $this->_id = $id;
    }
    function getId()
    {
        return $this->_id;
    }
    function getInsideItem()
    {
        return new Nest1();
    }
}
