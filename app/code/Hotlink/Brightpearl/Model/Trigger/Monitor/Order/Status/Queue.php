<?php
namespace Hotlink\Brightpearl\Model\Trigger\Monitor\Order\Status;

class Queue extends \Hotlink\Framework\Model\Trigger\AbstractTrigger
{

    /**
     * @var \Hotlink\Framework\Model\Stream\Magento\Model\ReaderFactory
     */
    protected $interactionStreamMagentoModelReaderFactory;

    public function __construct(
        \Hotlink\Framework\Helper\Exception $exceptionHelper,
        \Hotlink\Framework\Helper\Reflection $reflectionHelper,
        \Hotlink\Framework\Helper\Report $reportHelper,
        \Hotlink\Framework\Helper\Factory $factoryHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Hotlink\Framework\Model\Config\Map $configMap,
        \Hotlink\Framework\Model\UserFactory $userFactory,

        \Hotlink\Framework\Model\Stream\Magento\Model\ReaderFactory $interactionStreamMagentoModelReaderFactory
    ) {
        $this->interactionStreamMagentoModelReaderFactory = $interactionStreamMagentoModelReaderFactory;

        parent::__construct(
            $exceptionHelper,
            $reflectionHelper,
            $reportHelper,
            $factoryHelper,
            $storeManager,
            $configMap,
            $userFactory
        );
    }

    public function getMagentoEvents()
    {
        return [ 'hotlink_framework_monitor_order_status_queue' ];
    }

    public function getContexts()
    {
        return [ 'on_order_status_queued' => 'On order status queued' ];
    }

    public function getContext()
    {
        return 'on_order_status_queued';
    }

    protected function _getName()
    {
        return 'Order status queued';
    }

    protected function _execute()
    {
        $interaction = $this->getMagentoEvent()->getInteraction();
        $this->setInteractions( $interaction );

        $orders = $this->getMagentoEvent()->getCollection();
        $stream = $this->interactionStreamMagentoModelReaderFactory->create()->open( $orders );

        // env created for admin store. this is ok as interaction itself
        // creates separate envs for each payment.

        $environment = ( $interaction->hasEnvironment( $this->getStoreId() ) )
            ? $interaction->getEnvironment( $this->getStoreId() )
            : $interaction->newEnvironment( $this->getStoreId() );


        $environment->getParameter( 'stream' )->setValue($stream);

        return parent::_execute();
    }
}