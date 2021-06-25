<?php
namespace Hotlink\Brightpearl\Model\Trigger\Brightpearl\Order\Status;

class Notification extends \Hotlink\Framework\Model\Trigger\AbstractTrigger
{
    const KEY_ORDER_STATUS_MODIFIED   = 'on_order_status_modified_received';
    const LABEL_ORDER_STATUS_MODIFIED = 'On status modified notification (webhook)';
    const EVENT_ORDER_STATUS_MODIFIED = 'hotlink_brightpearl_order_status_modified_callback_received';
    const ORDER_STATUS_MODIFIED       = 'order.modified.order-status';

    protected function _getName()
    {
        return 'Order status notification';
    }

    public function getMagentoEvents()
    {
        return [ self::LABEL_ORDER_STATUS_MODIFIED => self::EVENT_ORDER_STATUS_MODIFIED ];
    }

    public function getContexts()
    {
        return [ self::KEY_ORDER_STATUS_MODIFIED => self::LABEL_ORDER_STATUS_MODIFIED ];
    }

    public function getContext()
    {
        $event = $this->getMagentoEvent();
        $order = $event->getOrder();

        $context = null;
        switch ($event->getName()) {

        case self::EVENT_ORDER_STATUS_MODIFIED:
            if ( $order['fullEvent'] == self::ORDER_STATUS_MODIFIED ) {
                $context = self::KEY_ORDER_STATUS_MODIFIED;
            }
            break;
        }

        return $context;
    }

    protected function _execute()
    {
        if ( $context = $this->getContext() ) {

            $order = $this->getMagentoEvent()->getOrder();
            $orderId = $order['id'];
            $storeId = $this->getStoreId();

            foreach ($this->getInteractions() as $interaction) {
                $interaction->setTrigger($this);

                if (!$interaction->hasEnvironment($storeId)) {
                    $interaction->createEnvironment($storeId);
                }

                $environment = $interaction->getEnvironment($storeId);
                $environment->getParameter( 'order_id' )->setValue($orderId);

                $interaction->execute();
            }
        }
    }
}
