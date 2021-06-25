<?php
namespace Hotlink\Framework\Helper;

/**
 *  @group  helper
 */

class ModuleTest extends \PHPUnit\Framework\TestCase
{

    function setUp()
    {
        $this->manager = \Magento\TestFramework\ObjectManager::getInstance();
    }

    function test_get_version()
    {
        $helper = $this->getHelper();
        $path = \Hotlink\Framework\Filesystem::getRelativePath( __FILE__, [ 'etc', 'module.xml' ], 3 );
        $config = @file_get_contents( $path );
        $matches = [];
        preg_match( '/setup_version="(.+)"/', $config, $matches );
        $this->assertEquals( $matches[ 1 ], $helper->getVersion( 'Hotlink_Framework' ) );
        $this->assertEquals( false, $helper->getVersion( 'Nonexistent_Module' ) );
    }

    function getHelper()
    {
        return $this->manager->create( '\Hotlink\Framework\Helper\Module' );
    }

}
