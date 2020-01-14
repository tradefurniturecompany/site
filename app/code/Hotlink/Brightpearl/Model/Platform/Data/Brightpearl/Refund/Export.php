<?php
namespace Hotlink\Brightpearl\Model\Platform\Data\Brightpearl\Refund;

class Export extends \Hotlink\Brightpearl\Model\Platform\Data
{

    protected function _map_object_magento( \Magento\Sales\Model\Order\Creditmemo $creditmemo, $extra )
    {
        $helper = $this->getHelper();
        $brightpearlOrderId = isset( $extra[ 'brightpearlOrderId' ] ) ? $extra[ 'brightpearlOrderId' ] : null;
        $paymentMethodCode  = isset( $extra[ 'paymentMethodCode' ] ) ? $extra[ 'paymentMethodCode' ] : null;

        $this[ "transactionRef"    ] = $creditmemo->getIncrementId();
        $this[ "paymentMethodCode" ] = $paymentMethodCode;
        $this[ "paymentType"       ] = "PAYMENT";
        $this[ "orderId"           ] = $brightpearlOrderId;
        $this[ "currencyIsoCode"   ] = $helper->getCurrencyCode( $creditmemo );
        $this[ "amountPaid"        ] = $helper->getGrandTotal( $creditmemo );
        $this[ "paymentDate"       ] = $helper->formatDate( $creditmemo->getCreatedAt() );
        $this[ "journalRef"        ] = "Refund for Magento credit note " . $creditmemo->getIncrementId();

    }

}