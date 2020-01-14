<?php
namespace Hotlink\Brightpearl\Test\unit\Model\Interaction\Order\Export\Helper\Customisation;

/**
 *
 *  @group  order
 *  @group  interaction
 *  @group  customisation
 *
 */

class OutputTest extends \PHPUnit\Framework\TestCase
{

    public function test_clean()
    {
        $output = $this->getOutputter();
        $this->assertEquals( 'abc', $output->clean( ' abc  ' ), "' abc  '" );
        $this->assertEquals( '1 23', $output->clean( '   1 23    ' ), "'   1 23    '" );
    }

    public function test_apply_success_nest0()
    {
        $output = $this->getOutputter();
        $target = $this->getNestedObject();

        $result = $output->apply( 'property', $target );
        $this->assertEquals( 2, count( $result ) );
        $this->assertSame( $target, $result[ 'object' ] );
        $this->assertEquals( 'property', $result[ 'index' ] );
    }

    public function test_apply_success_nest1_index()
    {
        $output = $this->getOutputter();
        $target = $this->getNestedObject();

        $result = $output->apply( 'inner1.property', $target );
        $this->assertEquals( 2, count( $result ) );
        $this->assertSame( $target[ 'inner1' ], $result[ 'object' ] );
        $this->assertEquals( 'property', $result[ 'index' ] );
    }

    public function test_apply_success_nest2_index_index()
    {
        $output = $this->getOutputter();
        $target = $this->getNestedObject();

        $result = $output->apply( 'inner1.inner2.property', $target );
        $this->assertEquals( 2, count( $result ) );
        $this->assertSame( $target[ 'inner1' ][ 'inner2'], $result[ 'object' ] );
        $this->assertEquals( 'property', $result[ 'index' ] );
    }

    /**
     *  @expectedException \Hotlink\Brightpearl\Model\Exception\Customisation\Parser
     *  @expectedExceptionMessage Empty output expression
     */
    public function test_apply_fail_empty_expression()
    {
        $output = $this->getOutputter();
        $output->apply( '', $this->getNestedObject() );
    }

    /**
     *  @expectedException \Hotlink\Brightpearl\Model\Exception\Customisation\Parser
     *  @expectedExceptionMessage missing index
     */
    public function test_apply_fail_invalid_expression()
    {
        $output = $this->getOutputter();
        $output->apply( 'froggy.pond', $this->getNestedObject() );
    }

    protected function getOutputter()
    {
        return new \Hotlink\Brightpearl\Model\Interaction\Order\Export\Helper\Customisation\Output();
    }

    public function getNestedObject()
    {
        $top = new \Magento\Framework\DataObject();
        $inner1 = new \Magento\Framework\DataObject();
        $inner2 = new \Magento\Framework\DataObject();
        $top[ 'inner1' ] = $inner1;
        $inner1[ 'inner2' ] = $inner2;
        return $top;
    }

}
