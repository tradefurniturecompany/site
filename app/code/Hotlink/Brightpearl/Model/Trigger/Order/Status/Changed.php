<?php
namespace Hotlink\Brightpearl\Model\Trigger\Order\Status;

class Changed extends \Hotlink\Framework\Model\Trigger\AbstractTrigger
{
    const KEY_STATUS_CHANGED   = 'on_status_changed';
    const LABEL_STATUS_CHANGED = 'On order status changed';

    protected $queueOrderHelper;
    protected $queueOrderStatusHelper;

    /**
     * @var \Magento\Framework\Data\CollectionFactory
     */
    protected $collectionFactory;

    function __construct(
        \Hotlink\Framework\Helper\Exception $exceptionHelper,
        \Hotlink\Framework\Helper\Reflection $reflectionHelper,
        \Hotlink\Framework\Helper\Report $reportHelper,
        \Hotlink\Framework\Helper\Factory $factoryHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Hotlink\Framework\Model\Config\Map $configMap,
        \Hotlink\Framework\Model\UserFactory $userFactory,

        \Hotlink\Brightpearl\Helper\Queue\Order $queueOrderHelper,
        \Hotlink\Brightpearl\Helper\Queue\Order\Status $queueOrderStatusHelper,
        \Magento\Framework\Data\CollectionFactory $collectionFactory
    ) {
        $this->queueOrderHelper = $queueOrderHelper;
        $this->queueOrderStatusHelper = $queueOrderStatusHelper;
        $this->collectionFactory = $collectionFactory;

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

    protected function _getName()
    {
        return 'Order status changed';
    }

    function getMagentoEvents()
    {
        return [ 'After order save' => 'sales_order_save_commit_after' ];
    }

    function getContexts()
    {
        return [ self::KEY_STATUS_CHANGED => self::LABEL_STATUS_CHANGED ];
    }

    function getContext()
    {
        $event = $this->getMagentoEvent();

        $context = null;
        switch ($event->getName()) {

        case 'sales_order_save_commit_after':
            $context = self::KEY_STATUS_CHANGED;
            break;
        }

        return $context;
    }

    protected function _execute()
    {
        $context = $this->getContext();
        if ( $context ) {

            $order = $this->getMagentoEvent()->getDataObject();
            if ( $order->getId() ) {

                $orderTracking  = $this->queueOrderHelper->getObject( $order );
                $statusTracking = $this->queueOrderStatusHelper->getObject( $order );

                //
                //  1. create a status queue record if appropriate
                //
                $this->_updateQueue( $order, $statusTracking, $orderTracking );

                //
                //  2. Launch interaction if something to send (avoiding noise of interactions that do nothing)
                //
                if ( $statusTracking->shouldSend( $orderTracking ) ) {

                    // prepare stream param
                    $collection = $this->collectionFactory->create();
                    $collection->addItem($order);
                    $storeId = $this->getStoreId();

                    foreach ($this->getInteractions() as $interaction) {
                        $interaction->setTrigger($this);

                        if (!$interaction->hasEnvironment($storeId))
                            $interaction->createEnvironment($storeId);

                        $environment = $interaction->getEnvironment($storeId);
                        $environment->getParameter('stream')->getValue()->open($collection);

                        $interaction->execute();
                    }
                }
            }
        }
    }

    protected function _updateQueue( \Magento\Sales\Model\Order $order,
                                     \Hotlink\Brightpearl\Model\Queue\Order\Status $statusTracking,
                                     \Hotlink\Brightpearl\Model\Queue\Order $orderTracking )
    {
        $inBp = $orderTracking->getInBp();
        if ( $inBp ) {

            $currStatus = $order->getData('status');
            $origStatus = $order->getOrigData('status');
            $hasStatusChanged =  $currStatus != $origStatus;

            if ( $hasStatusChanged ) {

                //
                // update status tracking to be sent to bp (send_to_bp=1)
                //
                $statusTracking
                    ->setSendToBp( 1 )
                    ->save();
            }
            else {
                // order status unchanged, nothing to send
            }
        }
        else {
            // do not execute status interaction (send to bp) if parent order is not in BP
            // do not create a new record if none exists
            // we don't explicitly set send_to_bp = 0, so that submittion can be controlled via database records
            // we only flag "send", we don't flag "do not send"
        }
    }


}