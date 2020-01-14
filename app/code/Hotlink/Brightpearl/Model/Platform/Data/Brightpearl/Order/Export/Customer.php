<?php
namespace Hotlink\Brightpearl\Model\Platform\Data\Brightpearl\Order\Export;

class Customer extends \Hotlink\Brightpearl\Model\Platform\Data
{

    protected function _map_object_magento( \Magento\Sales\Model\Order $order )
    {
        $helper = $this->getHelper();

        $this['email']      = $helper->getOrderCustomerEmail( $order );
        $this['salutation'] = $helper->getOrderCustomerSalutation( $order );
        $this['firstName']  = $helper->getOrderCustomerFirstName( $order );
        $this['lastName']   = $helper->getOrderCustomerLastName( $order );
        $this['company']    = $helper->getOrderCustomerCompany( $order );

        if ( $shipping = $order->getShippingAddress() )
            {
                $this[ 'shippingAddress' ] = $this->getObject( $shipping, 'Address' );
            }
        if ( $billing = $order->getBillingAddress() )
            {
                $this[ 'billingAddress' ] = $this->getObject( $billing, 'Address' );
            }
        if ( $helper->getIncludeMarketing() )
            {
                $this[ 'marketingDetails' ] = $this->getObject( $order, 'MarketingDetails' );
            }
        if ( $billing )
            {
                $this[ 'phone' ] = $billing->getTelephone();
            }
        else if ( $shipping )
            {
                $this[ 'phone' ] = $shipping->getTelephone();
            }
        $this[ 'preserveExistingContactData' ] = !$helper->getUpdateExistingCustomers();
        $this[ 'customFields' ] = $this->getObject( $order, 'CustomFields' );
    }

}