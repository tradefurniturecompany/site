<?php
namespace Hotlink\Brightpearl\Model\Trigger\Order\Payment;

class Export extends \Hotlink\Framework\Model\Trigger\AbstractTrigger
{

    const KEY_PAYMENT_UPDATED  = 'on_payment_updated';
    const KEY_ORDER_EXPORTED   = 'on_order_exported';

    const LABEL_PAYMENT_UPDATED  = 'On order payment update';
    const LABEL_ORDER_EXPORTED   = 'On order successfully exported';

    protected $queueOrderHelper;
    protected $queuePaymentHelper;

    protected $salesOrderFactory;
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
        \Hotlink\Brightpearl\Helper\Queue\Payment $queuePaymentHelper,
        \Magento\Sales\Model\OrderFactory $salesOrderFactory,
        \Magento\Framework\Data\CollectionFactory $collectionFactory
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
        $this->queueOrderHelper = $queueOrderHelper;
        $this->queuePaymentHelper = $queuePaymentHelper;
        $this->collectionFactory = $collectionFactory;
        $this->salesOrderFactory = $salesOrderFactory;
    }

    protected function _getName()
    {
        return 'Order payment export';
    }

    function getMagentoEvents()
    {
        return [ 'After successful Order Payment save'   => 'sales_order_payment_save_commit_after',
                 'After successful Order Payment export' => 'hotlink_brightpearl_order_exported' ];
    }

    function getContexts()
    {
        return [ self::KEY_PAYMENT_UPDATED => self::LABEL_PAYMENT_UPDATED,
                 self::KEY_ORDER_EXPORTED  => self::LABEL_ORDER_EXPORTED ];
    }

    function getContext()
    {
        $event = $this->getMagentoEvent();
        $context = null;
        switch ( $event->getName() )
            {
                case 'sales_order_payment_save_commit_after':
                    $context = self::KEY_PAYMENT_UPDATED;
                    break;

                case 'hotlink_brightpearl_order_exported':
                    $context = self::KEY_ORDER_EXPORTED;
                    break;

            }
        return $context;
    }

    protected function _execute()
    {
        if ( $payment = $this->_getPayment( $this->getContext() ) )
            {
                if ( $order = $this->_getOrder( $payment ) )
                    {
                        $paymentTracking = $this->queuePaymentHelper->getObject( $payment );
                        $orderTracking = $this->queueOrderHelper->getObject( $order );

                        //
                        //  1. create a payment queue record if appropriate
                        //
                        $this->_updateQueue( $payment, $paymentTracking, $orderTracking );

                        //
                        //  2. Launch interaction if something to send (avoiding noise of interactions that do nothing)
                        //
                        if ( $paymentTracking->shouldSend( $orderTracking ) )
                            {
                                $collection = $this->collectionFactory->create();
                                $collection->addItem( $payment );
                                foreach ( $this->getInteractions() as $interaction )
                                    {
                                        $storeId = $order->getStoreId();
                                        if ( $interaction->getConfig()->isEnabled( $storeId ) )
                                            {
                                                $interaction->setTrigger( $this );
                                                if ( !$interaction->hasEnvironment( $storeId ) )
                                                    {
                                                        $interaction->createEnvironment( $storeId );
                                                    }
                                                $environment = $interaction->getEnvironment( $storeId );
                                                $environment->getParameter( 'stream' )->getValue()->open( $collection );
                                                $interaction->execute();
                                            }
                                    }
                            }
                    }
                else
                    {
                        // orphaned payment, do nothing
                    }
            }
    }

    protected function _updateQueue( \Magento\Sales\Model\Order\Payment $payment,
                                     \Hotlink\Brightpearl\Model\Queue\Payment $paymentTracking,
                                     \Hotlink\Brightpearl\Model\Queue\Order $orderTracking
    )
    {
        $inBp = $orderTracking->getInBp();
        if ( $inBp )
            {
                $orig    = $payment->getOrigData();
                $data    = $payment->getData();
                $hasChanges = false;
                if ( is_array( $orig ) )
                    {
                        $current = array_intersect_key( $data, $orig ); // compare only common keys.
                        $hasChanges = ( $current != $orig ); // same key => value pairs. order doesn't matter.
                    }
                else
                    {
                        $hasChanges = true;
                    }

                if ( $hasChanges )
                    {
                        //
                        // update payment tracking to be sent to bp (send_to_bp=1)
                        //
                        $paymentTracking
                            ->setSendToBp( 1 )
                            ->save();
                    }
                else
                    {
                        // payment info unchanged, nothing to send
                    }
            }
        else
            {
                // do not execute payment interaction (send to bp) if parent order is not in BP
                // do not create a new record if none exists
                // we don't explicitly set send_to_bp = 0, so that submittion can be controlled via database records
                // we only flag "send", we don't flag "do not send"
            }
    }

    protected function _getPayment( $context )
    {
        $payment = false;
        if ( $context )
            {
                switch ( $context )
                    {
                        case self::KEY_ORDER_EXPORTED:
                            $payment = $this->getMagentoEvent()->getOrder()->getPayment();
                            break;

                        case self::KEY_PAYMENT_UPDATED:
                            $payment = $this->getMagentoEvent()->getDataObject();
                            break;

                        default:
                            break;
                    }
            }
        return $payment;
    }

    protected function _getOrder( \Magento\Sales\Model\Order\Payment $payment )
    {
        if ( $payment->getOrder() )
            {
                return $payment->getOrder();
            }
        $order = $this->salesOrderFactory->create()->load( $payment->getParentId() );
        if ( $order->getId() )
            {
                $payment->setOrder( $order );
                return $order;
            }
        return null;
    }

}