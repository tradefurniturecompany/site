<?php
namespace Hotlink\Framework\Test\integration\Helper;

/**
 *  @group  helper
 */

class HtmlTest extends \PHPUnit\Framework\TestCase
{

    public function setUp()
    {
        $this->manager = \Magento\TestFramework\ObjectManager::getInstance();
    }

    public function test_get_html_name()
    {
        $helper = $this->getHelper();
        $platform = $this->getPlatform();
        $this->assertEquals( 'Hotlink-Framework-Test-integration-Helper-HtmlTest', $helper->encode( $this ) );
        $this->assertEquals( 'Hotlink-Framework-Model-Platform', $helper->encode( $platform ) );
        $this->assertEquals( 'Hotlink-Framework-Helper-Thing', $helper->encode( 'Hotlink\Framework\Helper\Thing' ) );
        $this->assertEquals( 'Hotlink-Framework-Helper-Thing', $helper->encode( '\Hotlink\Framework\Helper\Thing' ) );
    }

    public function getHelper()
    {
        return $this->manager->create( '\Hotlink\Framework\Helper\Html' );
    }

    public function getPlatform()
    {
        return $this->manager->create( '\Hotlink\Framework\Model\Platform' );
    }

}
