<?php
namespace Hotlink\Brightpearl\Model\Trigger\Monitor\Order;

class Queue extends \Hotlink\Framework\Model\Trigger\AbstractTrigger
{

    /**
     *  This trigger responds to the event dispatched by the \Hotlink\Framework\Model\Monitor\Order\Queue monitor.
     *  It prepares interaction's environment and executes the interaction.
     */

    protected $streamReaderFactory;

    public function __construct(
        \Hotlink\Framework\Helper\Exception $exceptionHelper,
        \Hotlink\Framework\Helper\Reflection $reflectionHelper,
        \Hotlink\Framework\Helper\Report $reportHelper,
        \Hotlink\Framework\Helper\Factory $factoryHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Hotlink\Framework\Model\Config\Map $configMap,
        \Hotlink\Framework\Model\UserFactory $userFactory,

        \Hotlink\Framework\Model\Stream\Magento\Model\ReaderFactory $streamReaderFactory
    )
    {
        parent::__construct(
            $exceptionHelper,
            $reflectionHelper,
            $reportHelper,
            $factoryHelper,
            $storeManager,
            $configMap,
            $userFactory
        );
        $this->streamReaderFactory = $streamReaderFactory;
    }

    public function getMagentoEvents()
    {
        return [ 'hotlink_framework_monitor_order_queue' ];
    }

    public function getContexts()
    {
        return [ 'on_order_queued' => 'On order queued' ];
    }

    public function getContext()
    {
        return 'on_order_queued';
    }

    protected function _getName()
    {
        return 'Order queued';
    }

    protected function _execute()
    {
        $interaction = $this->getMagentoEvent()->getInteraction();
        $this->setInteractions( $interaction );

        $orders = $this->getMagentoEvent()->getCollection();
        $stream = $this->streamReaderFactory->create()->open( $orders );

        // env created for admin store. this is ok as interaction itself
        // creates separate envs for each processed order.

        $environment = ( $interaction->hasEnvironment( $this->getStoreId() ) )
                     ? $interaction->getEnvironment( $this->getStoreId() )
                     : $interaction->newEnvironment( $this->getStoreId() );

        $environment->getParameter( 'stream' )->setValue( $stream );

        return parent::_execute();
    }

}