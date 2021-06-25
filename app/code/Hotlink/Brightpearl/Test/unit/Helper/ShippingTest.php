<?php
namespace Hotlink\Brightpearl\Test\unit\Helper;

/**
 *
 *  @group  helper
 *  @group  shipping
 *
 */
class ShippingTest extends \PHPUnit\Framework\TestCase
{

    const SEP = "_";

    /**
     *  @expectedException \Hotlink\Framework\Model\Exception\Implementation
     */
    function test_encode_invalid_carrier_null()
    {
        $this->getHelper()->encode( null, null );
    }

    /**
     *  @expectedException \Hotlink\Framework\Model\Exception\Implementation
     */
    function test_encode_invalid_carrier_empty()
    {
        $this->getHelper()->encode( "", null );
    }

    /**
     *  @expectedException \Hotlink\Framework\Model\Exception\Implementation
     */
    function test_encode_invalid_carrier_false()
    {
        $this->getHelper()->encode( false, null );
    }

    /**
     *  @expectedException \Hotlink\Framework\Model\Exception\Implementation
     */
    function test_encode_invalid_carrier_zero()
    {
        $this->getHelper()->encode( 0, null );
    }

    /**
     *  @expectedException \Hotlink\Framework\Model\Exception\Processing
     */
    function test_encode_invalid_carrier_contains_separator()
    {
        $this->getHelper()->encode( "donkey" . self::SEP . "kong", null );
    }

    /**
     *  @expectedException \Hotlink\Framework\Model\Exception\Processing
     */
    function test_encode_invalid_carrier_startwith_separator()
    {
        $this->getHelper()->encode( self::SEP . "donkey", null );
    }

    function test_encode()
    {
        $helper = $this->getHelper();

        $this->assertEquals( 'a', $helper->encode( "a", "" ) );
        $this->assertEquals( 'abc', $helper->encode( "abc", "" ) );

        $this->assertEquals( 'a' . self::SEP . 'x', $helper->encode( "a", "x" ) );
        $this->assertEquals( 'abc' . self::SEP . 'xyz', $helper->encode( "abc", "xyz" ) );
    }

    /**
     *  @expectedException \Hotlink\Framework\Model\Exception\Implementation
     */
    function test_decode_invalid_null()
    {
        $this->getHelper()->decodeCarrier( null );
    }

    /**
     *  @expectedException \Hotlink\Framework\Model\Exception\Implementation
     */
    function test_decode_invalid_empty()
    {
        $this->getHelper()->decodeCarrier( "" );
    }

    /**
     *  @expectedException \Hotlink\Framework\Model\Exception\Implementation
     */
    function test_decode_invalid_false()
    {
        $this->getHelper()->decodeCarrier( false );
    }

    /**
     *  @expectedException \Hotlink\Framework\Model\Exception\Implementation
     */
    function test_decode_invalid_zero()
    {
        $this->getHelper()->decodeCarrier( 0 );
    }

    /**
     *  @expectedException \Hotlink\Framework\Model\Exception\Implementation
     */
    function test_decode_invalid_only_separators()
    {
        $this->getHelper()->decodeCarrier( self::SEP );
    }

    /**
     *  @expectedException \Hotlink\Framework\Model\Exception\Implementation
     */
    function test_decode_invalid_only_separators_multiple()
    {
        $this->getHelper()->decodeCarrier( self::SEP . self::SEP . self::SEP );
    }

    function test_decode_carrier()
    {
        $s = self::SEP;
        $helper = $this->getHelper();
        $this->assertEquals( 'ok', $helper->decodeCarrier( "ok" ) );
        $this->assertEquals( 'ok', $helper->decodeCarrier( "ok" . $s . "too" ) );
        $this->assertEquals( 'ok', $helper->decodeCarrier( "ok" . $s . "too" . $s . "also" ) );
    }

    function test_decode_method()
    {
        $s = self::SEP;
        $helper = $this->getHelper();
        $this->assertEquals( '', $helper->decodeMethod( "abc" ) );
        $this->assertEquals( 'xyz', $helper->decodeMethod( "abc" . $s . "xyz" ) );
        $this->assertEquals( 'xyz' . $s . '123', $helper->decodeMethod( "abc" . $s . "xyz" . $s . "123" ) );
    }

    protected function getHelper()
    {
        $exception = new \Hotlink\Framework\Helper\Exception();
        return new \Hotlink\Brightpearl\Helper\Shipping( $exception );
    }

}
