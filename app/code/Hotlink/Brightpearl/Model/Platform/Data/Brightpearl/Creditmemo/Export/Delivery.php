<?php
namespace Hotlink\Brightpearl\Model\Platform\Data\Brightpearl\Creditmemo\Export;

class Delivery extends \Hotlink\Brightpearl\Model\Platform\Data
{

    protected function _map_object_magento( \Magento\Sales\Model\Order\Creditmemo $creditmemo )
    {

        $helper = $this->getHelper();

        $this[ "date"  ] = $helper->formatDate( $creditmemo->getCreatedAt() );
    }

}