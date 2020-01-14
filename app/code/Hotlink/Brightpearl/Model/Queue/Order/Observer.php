<?php
namespace Hotlink\Brightpearl\Model\Queue\Order;

class Observer implements \Magento\Framework\Event\ObserverInterface
{

    //
    // Listens to sales_order_save_after
    //
    // Creates an order tracking queue record. In M2 this event resides within the order save database transaction, ensuring atomicity.
    //

    protected $interaction;
    protected $queueOrderHelper;

    public function __construct(
        \Hotlink\Brightpearl\Model\Interaction\Order\Export $interaction,
        \Hotlink\Brightpearl\Helper\Queue\Order $queueOrderHelper
    )
    {
        $this->interaction = $interaction;
        $this->queueOrderHelper = $queueOrderHelper;
    }

    public function execute( \Magento\Framework\Event\Observer $observer )
    {
        if ( $order = $observer->getOrder() )
            {
                if ( $this->interaction->getConfig()->isEnabled( $order->getStoreId() ) )
                    {
                        $tracking = $this->queueOrderHelper->getObject( $order );
                        $tracking
                            ->setSendToBp( true )
                            ->save();  // saved iff the transaction is committed
                    }
            }
    }

}
