<?php
namespace Hotlink\Framework\Test\integration\Helper;

/**
 *  @group  helper
 */

class ReflectionTest extends \PHPUnit\Framework\TestCase
{

    function setUp()
    {
        $this->manager = \Magento\TestFramework\ObjectManager::getInstance();
    }

    function test_get_module()
    {
        $helper = $this->getHelper();
        $platform = $this->getPlatform();
        $this->assertEquals( 'Hotlink_Framework', $helper->getModule( $platform ) );
        $this->assertEquals( 'Hotlink_Framework', $helper->getModule( 'Hotlink\Framework\Helper\Thing' ) );
        $this->assertEquals( 'Hotlink_Framework', $helper->getModule( '\Hotlink\Framework\Helper\Thing' ) );
        $this->assertEquals( false, $helper->getModule( null ) );
        $this->assertEquals( false, $helper->getModule( false ) );
    }

    function test_get_class()
    {
        $helper = $this->getHelper();
        $platform = $this->getPlatform();

        $this->assertEquals( '', $helper->getClass( null ) );

        $this->assertEquals( '\Hotlink\Framework\Model\Platform', $helper->getClass( $platform ) );
        $this->assertEquals( '\Hotlink\Framework\Test\integration\Helper\ReflectionTest', $helper->getClass( $this ) );
        $this->assertEquals( '\Hotlink\Framework\Test', $helper->getClass( '\Hotlink\Framework\Test' ) );
        $this->assertEquals( '\Hotlink\Framework\Test', $helper->getClass( 'Hotlink\Framework\Test' ) );

        $this->assertEquals( '\Hotlink\Framework\Model\Platform\More', $helper->getClass( $platform, [ 'More' ] ) );
        $this->assertEquals( '\Hotlink\Framework\Test\And\Or', $helper->getClass( '\Hotlink\Framework\Test', [ 'And', 'Or' ] ) );
        $this->assertEquals( '\Hotlink\Framework\Test\And\Or', $helper->getClass( 'Hotlink\Framework\Test', [ 'And', 'Or' ] ) );

    }

    function getHelper()
    {
        return $this->manager->create( '\Hotlink\Framework\Helper\Reflection' );
    }

    function getPlatform()
    {
        return $this->manager->create( '\Hotlink\Framework\Model\Platform' );
    }

}