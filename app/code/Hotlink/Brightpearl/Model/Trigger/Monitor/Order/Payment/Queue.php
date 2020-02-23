<?php
namespace Hotlink\Brightpearl\Model\Trigger\Monitor\Order\Payment;

class Queue extends \Hotlink\Framework\Model\Trigger\AbstractTrigger
{

    /**
     *  This trigger responds to the event dispatched by the \Hotlink\Framework\Model\Monitor\Order\Payment\Queue monitor.
     *  It prepares interaction's environment and executes the interaction.
     */

    protected $streamReaderFactory;

    function __construct(
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

    function getMagentoEvents()
    {
        return [ 'hotlink_framework_monitor_order_payment_queue' ];
    }

    function getContexts()
    {
        return [ 'on_order_payment_queued' => 'On order payment queued' ];
    }

    function getContext()
    {
        return 'on_order_payment_queued';
    }

    protected function _getName()
    {
        return 'Order payment queued';
    }

    protected function _execute()
    {
        $interaction = $this->getMagentoEvent()->getInteraction();
        $this->setInteractions( $interaction );
        
        $payments = $this->getMagentoEvent()->getCollection();
        $stream = $this->streamReaderFactory->create()->open( $payments );

        // env created for admin store. this is ok as interaction itself
        // creates separate envs for each payment.

        $environment = ( $interaction->hasEnvironment( $this->getStoreId() ) )
                     ? $interaction->getEnvironment( $this->getStoreId() )
                     : $interaction->newEnvironment( $this->getStoreId() );

        $environment->getParameter( 'stream' )->setValue( $stream );

        return parent::_execute();
    }

}