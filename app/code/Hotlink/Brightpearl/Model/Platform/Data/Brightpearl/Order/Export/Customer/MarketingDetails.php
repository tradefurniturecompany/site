<?php
namespace Hotlink\Brightpearl\Model\Platform\Data\Brightpearl\Order\Export\Customer;

class MarketingDetails extends \Hotlink\Brightpearl\Model\Platform\Data
{

    protected function _map_object_magento( \Magento\Sales\Model\Order $order )
    {
        $helper = $this->getHelper();
        $this['isReceiveEmailNewsletter'] = $helper->isCustomerSubscribedToNewsletter( $order );
    }

}