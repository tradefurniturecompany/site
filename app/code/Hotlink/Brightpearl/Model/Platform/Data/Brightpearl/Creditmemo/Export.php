<?php
namespace Hotlink\Brightpearl\Model\Platform\Data\Brightpearl\Creditmemo;

class Export extends \Hotlink\Brightpearl\Model\Platform\Data
{

    protected function _map_object_magento( \Magento\Sales\Model\Order\Creditmemo $creditmemo, $brightpearlOrder )
    {

        $helper = $this->getHelper();

        $this[ "customerId"  ] = $brightpearlOrder[ 'parties' ][ 'billing' ][ 'contactId' ];
        $this[ "parentId"    ] = $brightpearlOrder[ 'id' ];
        $this[ "ref"         ] = "Magento credit note " . $creditmemo->getIncrementId();
        $this[ "placedOn"    ] = $helper->formatDate( $creditmemo->getCreatedAt() );
        $this[ "statusId"    ] = $helper->getSalesCreditOrderStatus();
        $this[ "channelId"   ] = $helper->getCreditmemoChannelId();
        $this[ "externalRef" ] = $creditmemo->getIncrementId();

        // these properties are set by the caller, when appropriate
        // $this[ "warehouseId" ] = $helper->getQuarantineWarehouse();
        // $this[ "priceListId" ] = $helper->getQuarantinePricelist();

        $this[ 'currency' ]  = $this->getObject( $creditmemo, 'Currency', true );
        $this[ 'delivery' ]  = $this->getObject( $creditmemo, 'Delivery', true );
        $this[ 'rows' ]      = $this->getObject( $creditmemo, 'Rows', true, $brightpearlOrder );

    }

}