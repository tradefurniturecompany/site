<?php
namespace Hotlink\Brightpearl\Model\Platform\Data\Brightpearl\Order\Payments;

class DefaultPayments extends \Hotlink\Brightpearl\Model\Platform\Data
{

    protected function _map_object_magento( \Magento\Sales\Model\Order\Payment $payment )
    {
        $helper = $this->getHelper();
        $this['createSalesReceipts']     = $helper->getPaymentCreateSalesReceipts( $payment );
        $this['totalAmountPaid']         = (double)$helper->getOrderTotalAmountPaid( $payment->getOrder() );
        $this['amountPaid']              = (double)$helper->getPaymentAmountPaid( $payment );
        $this['fullyPaid']               = $helper->getOrderFullyPaid( $payment->getOrder() );
        $this['description']             = $helper->getPaymentDescription( $payment );
        $this['nominalCode']             = $helper->getPaymentNominalCode( $payment );
    }

}