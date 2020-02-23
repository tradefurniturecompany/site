<?php
namespace Hotlink\Framework\Test\integration\Model\Config;

/**
 *  @group  config
 */
class MapTest extends \PHPUnit\Framework\TestCase
{

    //
    //  Must overload to set factory correctly
    //
    function setUp()
    {
        $this->manager = \Magento\TestFramework\ObjectManager::getInstance();
    }

    function test_platforms()
    {
        $map = $this->getMapTestObject();

        $platforms = $map->getPlatforms();
        $this->assertEquals( 3, count( $platforms ) );
        $this->assertContains( '\Hotlink\Framework\Model\Platform', $platforms );
        $this->assertContains( '\Hotlink\Framework\Model\Platform2', $platforms );
        $this->assertContains( '\Hotlink\Framework\Model\Platform3', $platforms );
    }

    function test_platform()
    {
        $map = $this->getMapTestObject();

        $this->assertEquals( '\Hotlink\Framework\Model\Platform', $map->getPlatform( '\Hotlink\Framework\Model\Interaction\Log\Cleaning' ) );
        $this->assertEquals( '\Hotlink\Framework\Model\Platform', $map->getPlatform( '\Hotlink\Framework\Module1\InteractionA' ) );
        $this->assertEquals( '\Hotlink\Framework\Model\Platform', $map->getPlatform( '\Hotlink\Framework\Module1\InteractionB' ) );

        $this->assertEquals( '\Hotlink\Framework\Model\Platform2', $map->getPlatform( '\Hotlink\Framework\Module2\InteractionA' ) );
        $this->assertEquals( '\Hotlink\Framework\Model\Platform2', $map->getPlatform( '\Hotlink\Framework\Module2\InteractionB' ) );

        $this->assertEquals( '\Hotlink\Framework\Model\Platform3', $map->getPlatform( '\Hotlink\Framework\Module3\InteractionA' ) );
        $this->assertEquals( '\Hotlink\Framework\Model\Platform3', $map->getPlatform( '\Hotlink\Framework\Module3\InteractionB' ) );
    }

    function test_monitors()
    {
        $map = $this->getMapTestObject();

        $monitors = $map->getMonitors();
        $this->assertEquals( 3, count( $monitors ) );
        $this->assertTrue( is_string( $monitors[ 0 ] ) );
        $this->assertContains( '\Hotlink\Framework\Model\Monitor\Cron', $monitors );
        $this->assertContains( '\Hotlink\Framework\Model\Monitor\OrderStatus', $monitors );
        $this->assertContains( '\Hotlink\Framework\Model\Monitor\LostItems', $monitors );

        $monitors = $map->getMonitors( '\Hotlink\Framework\Model\Interaction\Log\Cleaning' );
        $this->assertEquals( 1, count( $monitors ) );
        $this->assertContains( '\Hotlink\Framework\Model\Monitor\Cron', $monitors );

        $monitors = $map->getMonitors( '\Hotlink\Framework\Module1\InteractionA' );
        $this->assertEquals( 3, count( $monitors ) );
        $this->assertContains( '\Hotlink\Framework\Model\Monitor\Cron', $monitors );
        $this->assertContains( '\Hotlink\Framework\Model\Monitor\OrderStatus', $monitors );
        $this->assertContains( '\Hotlink\Framework\Model\Monitor\LostItems', $monitors );

        $monitors = $map->getMonitors( '\Hotlink\Framework\Module1\InteractionB' );
        $this->assertEquals( 2, count( $monitors ) );
        $this->assertContains( '\Hotlink\Framework\Model\Monitor\Cron', $monitors );
        $this->assertContains( '\Hotlink\Framework\Model\Monitor\OrderStatus', $monitors );
        
        $monitors = $map->getMonitors( '\Hotlink\Framework\Module2\InteractionA' );
        $this->assertEquals( 2, count( $monitors ) );
        $this->assertContains( '\Hotlink\Framework\Model\Monitor\Cron', $monitors );
        $this->assertContains( '\Hotlink\Framework\Model\Monitor\LostItems', $monitors );

        $monitors = $map->getMonitors( '\Hotlink\Framework\Module2\InteractionB' );
        $this->assertEquals( 1, count( $monitors ) );
        $this->assertContains( '\Hotlink\Framework\Model\Monitor\Cron', $monitors );

        $monitors = $map->getMonitors( '\Hotlink\Framework\Module3\InteractionA' );
        $this->assertEquals( 2, count( $monitors ) );
        $this->assertContains( '\Hotlink\Framework\Model\Monitor\Cron', $monitors );
        $this->assertContains( '\Hotlink\Framework\Model\Monitor\LostItems', $monitors );

        $monitors = $map->getMonitors( '\Hotlink\Framework\Module3\InteractionB' );
        $this->assertEquals( 1, count( $monitors ) );
        $this->assertContains( '\Hotlink\Framework\Model\Monitor\Cron', $monitors );

    }

    function test_triggers()
    {
        $map = $this->getMapTestObject();

        $triggers = $map->getTriggers();
        $this->assertEquals( 4, count( $triggers ) );
        $this->assertTrue( is_string( $triggers[ 0 ] ) );
        $this->assertContains( '\Hotlink\Framework\Model\Trigger\Monitor\Cron', $triggers );
        $this->assertContains( '\Hotlink\Framework\Model\Trigger\Order\Complete', $triggers );
        $this->assertContains( '\Hotlink\Framework\Model\Trigger\Payment\Complete', $triggers );
        $this->assertContains( '\Hotlink\Framework\Model\Trigger\Checkout\Start', $triggers );

        $triggers = $map->getTriggers( '\Hotlink\Framework\Model\Interaction\Log\Cleaning' );
        $this->assertEquals( 1, count( $triggers ) );
        $this->assertContains( '\Hotlink\Framework\Model\Trigger\Monitor\Cron', $triggers );

        $triggers = $map->getTriggers( '\Hotlink\Framework\Module1\InteractionA' );
        $this->assertEquals( 1, count( $triggers ) );
        $this->assertContains( '\Hotlink\Framework\Model\Trigger\Monitor\Cron', $triggers );

        $triggers = $map->getTriggers( '\Hotlink\Framework\Module1\InteractionB' );
        $this->assertEquals( 1, count( $triggers ) );
        $this->assertContains( '\Hotlink\Framework\Model\Trigger\Monitor\Cron', $triggers );
        
        $triggers = $map->getTriggers( '\Hotlink\Framework\Module2\InteractionA' );
        $this->assertEquals( 4, count( $triggers ) );
        $this->assertContains( '\Hotlink\Framework\Model\Trigger\Monitor\Cron', $triggers );
        $this->assertContains( '\Hotlink\Framework\Model\Trigger\Order\Complete', $triggers );
        $this->assertContains( '\Hotlink\Framework\Model\Trigger\Payment\Complete', $triggers );
        $this->assertContains( '\Hotlink\Framework\Model\Trigger\Checkout\Start', $triggers );

        $triggers = $map->getTriggers( '\Hotlink\Framework\Module2\InteractionB' );
        $this->assertEquals( 1, count( $triggers ) );
        $this->assertContains( '\Hotlink\Framework\Model\Trigger\Monitor\Cron', $triggers );

        $triggers = $map->getTriggers( '\Hotlink\Framework\Module3\InteractionA' );
        $this->assertEquals( 2, count( $triggers ) );
        $this->assertContains( '\Hotlink\Framework\Model\Trigger\Monitor\Cron', $triggers );
        $this->assertContains( '\Hotlink\Framework\Model\Trigger\Order\Complete', $triggers );

        $triggers = $map->getTriggers( '\Hotlink\Framework\Module3\InteractionB' );
        $this->assertEquals( 2, count( $triggers ) );
        $this->assertContains( '\Hotlink\Framework\Model\Trigger\Monitor\Cron', $triggers );
        $this->assertContains( '\Hotlink\Framework\Model\Trigger\Payment\Complete', $triggers );
    }

    function test_triggers_by_interactions()
    {
        $map = $this->getMapTestObject();

        $triggers = $map->getTriggers( '\Hotlink\Framework\Model\Interaction\Log\Cleaning' );
        $this->assertEquals( 1, count( $triggers ) );
        $this->assertTrue( is_string( $triggers[ 0 ] ) );
        $this->assertContains( '\Hotlink\Framework\Model\Trigger\Monitor\Cron', $triggers );

        $triggers = $map->getTriggers( '\Hotlink\Framework\Module1\InteractionA' );
        $this->assertEquals( 1, count( $triggers ) );
        $this->assertTrue( is_string( $triggers[ 0 ] ) );
        $this->assertContains( '\Hotlink\Framework\Model\Trigger\Monitor\Cron', $triggers );

        $triggers = $map->getTriggers( '\Hotlink\Framework\Module1\InteractionB' );
        $this->assertEquals( 1, count( $triggers ) );
        $this->assertTrue( is_string( $triggers[ 0 ] ) );
        $this->assertContains( '\Hotlink\Framework\Model\Trigger\Monitor\Cron', $triggers );

        $triggers = $map->getTriggers( '\Hotlink\Framework\Module2\InteractionA' );
        $this->assertEquals( 4, count( $triggers ) );
        $this->assertTrue( is_string( $triggers[ 0 ] ) );
        $this->assertContains( '\Hotlink\Framework\Model\Trigger\Monitor\Cron', $triggers );
        $this->assertContains( '\Hotlink\Framework\Model\Trigger\Order\Complete', $triggers );
        $this->assertContains( '\Hotlink\Framework\Model\Trigger\Payment\Complete', $triggers );
        $this->assertContains( '\Hotlink\Framework\Model\Trigger\Checkout\Start', $triggers );

        $triggers = $map->getTriggers( '\Hotlink\Framework\Module2\InteractionB' );
        $this->assertEquals( 1, count( $triggers ) );
        $this->assertTrue( is_string( $triggers[ 0 ] ) );
        $this->assertContains( '\Hotlink\Framework\Model\Trigger\Monitor\Cron', $triggers );

        $triggers = $map->getTriggers( '\Hotlink\Framework\Module3\InteractionA' );
        $this->assertEquals( 2, count( $triggers ) );
        $this->assertTrue( is_string( $triggers[ 0 ] ) );
        $this->assertContains( '\Hotlink\Framework\Model\Trigger\Monitor\Cron', $triggers );
        $this->assertContains( '\Hotlink\Framework\Model\Trigger\Order\Complete', $triggers );

        $triggers = $map->getTriggers( '\Hotlink\Framework\Module3\InteractionB' );
        $this->assertEquals( 2, count( $triggers ) );
        $this->assertTrue( is_string( $triggers[ 0 ] ) );
        $this->assertContains( '\Hotlink\Framework\Model\Trigger\Monitor\Cron', $triggers );
        $this->assertContains( '\Hotlink\Framework\Model\Trigger\Payment\Complete', $triggers );
    }

    function test_interactions()
    {
        $map = $this->getMapTestObject();

        $interactions = $map->getInteractions();
        $this->assertEquals( 7, count( $interactions ) );
        $this->assertTrue( is_string( $interactions[ 0 ] ) );
        $this->assertContains( '\Hotlink\Framework\Model\Interaction\Log\Cleaning', $interactions );
        $this->assertContains( '\Hotlink\Framework\Module1\InteractionA', $interactions );
        $this->assertContains( '\Hotlink\Framework\Module1\InteractionB', $interactions );
        $this->assertContains( '\Hotlink\Framework\Module2\InteractionA', $interactions );
        $this->assertContains( '\Hotlink\Framework\Module2\InteractionB', $interactions );
        $this->assertContains( '\Hotlink\Framework\Module3\InteractionA', $interactions );
        $this->assertContains( '\Hotlink\Framework\Module3\InteractionB', $interactions );
    }

    function test_interactions_by_platform()
    {
        $map = $this->getMapTestObject();

        $interactions = $map->getInteractions( '\Hotlink\Framework\Model\Platform' );
        $this->assertEquals( 3, count( $interactions ) );
        $this->assertTrue( is_string( $interactions[ 0 ] ) );
        $this->assertContains( '\Hotlink\Framework\Model\Interaction\Log\Cleaning', $interactions );
        $this->assertContains( '\Hotlink\Framework\Module1\InteractionA', $interactions );
        $this->assertContains( '\Hotlink\Framework\Module1\InteractionB', $interactions );

        $interactions = $map->getInteractions( '\Hotlink\Framework\Model\Platform2' );
        $this->assertEquals( 2, count( $interactions ) );
        $this->assertTrue( is_string( $interactions[ 0 ] ) );
        $this->assertContains( '\Hotlink\Framework\Module2\InteractionA', $interactions );
        $this->assertContains( '\Hotlink\Framework\Module2\InteractionB', $interactions );

        $interactions = $map->getInteractions( '\Hotlink\Framework\Model\Platform3' );
        $this->assertEquals( 2, count( $interactions ) );
        $this->assertTrue( is_string( $interactions[ 0 ] ) );
        $this->assertContains( '\Hotlink\Framework\Module3\InteractionA', $interactions );
        $this->assertContains( '\Hotlink\Framework\Module3\InteractionB', $interactions );
    }

    function test_interactions_by_trigger()
    {
        $map = $this->getMapTestObject();

        $interactions = $map->getInteractions( '\Hotlink\Framework\Model\Trigger\Monitor\Cron' );
        $this->assertEquals( 7, count( $interactions ) );
        $this->assertTrue( is_string( $interactions[ 0 ] ) );
        $this->assertContains( '\Hotlink\Framework\Model\Interaction\Log\Cleaning', $interactions );
        $this->assertContains( '\Hotlink\Framework\Module1\InteractionA', $interactions );
        $this->assertContains( '\Hotlink\Framework\Module1\InteractionB', $interactions );
        $this->assertContains( '\Hotlink\Framework\Module2\InteractionA', $interactions );
        $this->assertContains( '\Hotlink\Framework\Module2\InteractionB', $interactions );
        $this->assertContains( '\Hotlink\Framework\Module3\InteractionA', $interactions );
        $this->assertContains( '\Hotlink\Framework\Module3\InteractionB', $interactions );

        $interactions = $map->getInteractions( '\Hotlink\Framework\Model\Trigger\Order\Complete' );
        $this->assertEquals( 2, count( $interactions ) );
        $this->assertTrue( is_string( $interactions[ 0 ] ) );
        $this->assertContains( '\Hotlink\Framework\Module2\InteractionA', $interactions );
        $this->assertContains( '\Hotlink\Framework\Module3\InteractionA', $interactions );

        $interactions = $map->getInteractions( '\Hotlink\Framework\Model\Trigger\Payment\Complete' );
        $this->assertEquals( 2, count( $interactions ) );
        $this->assertTrue( is_string( $interactions[ 0 ] ) );
        $this->assertContains( '\Hotlink\Framework\Module2\InteractionA', $interactions );
        $this->assertContains( '\Hotlink\Framework\Module3\InteractionB', $interactions );

        $interactions = $map->getInteractions( '\Hotlink\Framework\Model\Trigger\Checkout\Start' );
        $this->assertEquals( 1, count( $interactions ) );
        $this->assertTrue( is_string( $interactions[ 0 ] ) );
        $this->assertContains( '\Hotlink\Framework\Module2\InteractionA', $interactions );
    }

    function test_interactions_by_monitor()
    {
        $map = $this->getMapTestObject();

        $interactions = $map->getInteractions( '\Hotlink\Framework\Model\Monitor\Cron' );
        $this->assertEquals( 7, count( $interactions ) );
        $this->assertTrue( is_string( $interactions[ 0 ] ) );
        $this->assertContains( '\Hotlink\Framework\Model\Interaction\Log\Cleaning', $interactions );
        $this->assertContains( '\Hotlink\Framework\Module1\InteractionA', $interactions );
        $this->assertContains( '\Hotlink\Framework\Module1\InteractionB', $interactions );
        $this->assertContains( '\Hotlink\Framework\Module2\InteractionA', $interactions );
        $this->assertContains( '\Hotlink\Framework\Module2\InteractionB', $interactions );
        $this->assertContains( '\Hotlink\Framework\Module3\InteractionA', $interactions );
        $this->assertContains( '\Hotlink\Framework\Module3\InteractionB', $interactions );

        $interactions = $map->getInteractions( '\Hotlink\Framework\Model\Monitor\OrderStatus' );
        $this->assertEquals( 2, count( $interactions ) );
        $this->assertTrue( is_string( $interactions[ 0 ] ) );
        $this->assertContains( '\Hotlink\Framework\Module1\InteractionA', $interactions );
        $this->assertContains( '\Hotlink\Framework\Module1\InteractionB', $interactions );

        $interactions = $map->getInteractions( '\Hotlink\Framework\Model\Monitor\LostItems' );
        $this->assertEquals( 3, count( $interactions ) );
        $this->assertTrue( is_string( $interactions[ 0 ] ) );
        $this->assertContains( '\Hotlink\Framework\Module1\InteractionA', $interactions );
        $this->assertContains( '\Hotlink\Framework\Module2\InteractionA', $interactions );
        $this->assertContains( '\Hotlink\Framework\Module3\InteractionA', $interactions );
    }

    function test_actions()
    {
        $map = $this->getMapTestObject();

        $actions = $map->getActions();
        $this->assertEquals( 3, count( $actions ) );
        $this->assertTrue( is_string( $actions[ 0 ] ) );
        $this->assertContains( '\Hotlink\Framework\Model\Action\Index', $actions );
        $this->assertContains( '\Hotlink\Framework\Model\Action\Email', $actions );
        $this->assertContains( '\Hotlink\Framework\Model\Action\Cleanup', $actions );
    }

    function test_actions_by_interaction()
    {
        $map = $this->getMapTestObject();

        $actions = $map->getActions( '\Hotlink\Framework\Model\Interaction\Log\Cleaning' );
        $this->assertEquals( 1, count( $actions ) );
        $this->assertTrue( is_string( $actions[ 0 ] ) );
        $this->assertContains( '\Hotlink\Framework\Model\Action\Index', $actions );

        $actions = $map->getActions( '\Hotlink\Framework\Module1\InteractionA' );
        $this->assertEquals( 0, count( $actions ) );

        $actions = $map->getActions( '\Hotlink\Framework\Module1\InteractionB' );
        $this->assertEquals( 1, count( $actions ) );
        $this->assertTrue( is_string( $actions[ 0 ] ) );
        $this->assertContains( '\Hotlink\Framework\Model\Action\Index', $actions );

        $actions = $map->getActions( '\Hotlink\Framework\Module2\InteractionA' );
        $this->assertEquals( 2, count( $actions ) );
        $this->assertTrue( is_string( $actions[ 0 ] ) );
        $this->assertContains( '\Hotlink\Framework\Model\Action\Index', $actions );
        $this->assertContains( '\Hotlink\Framework\Model\Action\Email', $actions );

        $actions = $map->getActions( '\Hotlink\Framework\Module2\InteractionB' );
        $this->assertEquals( 1, count( $actions ) );
        $this->assertTrue( is_string( $actions[ 0 ] ) );
        $this->assertContains( '\Hotlink\Framework\Model\Action\Cleanup', $actions );

        $actions = $map->getActions( '\Hotlink\Framework\Module3\InteractionA' );
        $this->assertEquals( 1, count( $actions ) );
        $this->assertTrue( is_string( $actions[ 0 ] ) );
        $this->assertContains( '\Hotlink\Framework\Model\Action\Email', $actions );

        $actions = $map->getActions( '\Hotlink\Framework\Module3\InteractionB' );
        $this->assertEquals( 0, count( $actions ) );
    }

    function getMapTestObject()
    {
        // $map = $this->manager->create( '\Hotlink\Framework\Model\Config\Map' );
        // return $map;

        // $root = \Hotlink\Framework\Filesystem::getRelativePath( __FILE__ );
        // $fileResolver = $this->manager->create( '\Hotlink\Framework\Test\integration\Model\Config\MapTest\File\Resolver', [ 'root' => $root ] );
        // $schemaLocator = $this->manager->create( '\Hotlink\Framework\Test\integration\Model\Config\MapTest\Schema\Locator' );
        // $reader = $this->manager->create( '\Hotlink\Framework\Model\Config\Map\Reader', [ 'schemaLocator' => $schemaLocator,
        //                                                                                   'fileResolver' => $fileResolver ] );
        // $map = $this->manager->create( '\Hotlink\Framework\Model\Config\Map', [ 'reader' => $reader ] );
        // return $map;

        $root = \Hotlink\Framework\Filesystem::getRelativePath( __FILE__ );
        $fileResolver = $this->manager->create( '\Hotlink\Framework\Test\integration\Model\Config\MapTest\File\Resolver', [ 'root' => $root ] );
        $reader = $this->manager->create( '\Hotlink\Framework\Model\Config\Map\Reader', [ 'fileResolver' => $fileResolver ] );
        $map = $this->manager->create( '\Hotlink\Framework\Model\Config\Map', [ 'reader' => $reader ] );
        return $map;
    }

}