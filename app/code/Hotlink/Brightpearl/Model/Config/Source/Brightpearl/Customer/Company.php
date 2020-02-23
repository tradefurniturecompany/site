<?php
namespace Hotlink\Brightpearl\Model\Config\Source\Brightpearl\Customer;

class Company implements \Magento\Framework\Option\ArrayInterface
{
    const LEAVE_EMPTY               = '';                     // ignore
    const CUSTOMER                  = 'customer';             // from customer attribute "company"
    const BILLING                   = 'billing';              // from order billing address attribute "company"
    const SHIPPING                  = 'shipping';             // from order shipping address attribute "company"

    function toOptionArray()
    {
        return array(
            array( 'value' => self::LEAVE_EMPTY,
                   'label' => __( 'Leave empty' ) ),
            array( 'value' => self::CUSTOMER,
                   'label' => __( 'Customer attribute' ) ),
            array( 'value' => self::BILLING,
                   'label' => __( 'Order Billing Address attribute' ) ),
            array( 'value' => self::SHIPPING,
                   'label' => __( 'Order Shipping Address attribute' ) )
        );
    }

    function toArray()
    {
        return array(
            self::LEAVE_EMPTY => __( 'Leave Empty' ),
            self::CUSTOMER    => __( 'Customer attribute' ),
            self::BILLING     => __( 'Order Billing Address attribute' ),
            self::SHIPPING    => __( 'Order Shipping Address attribute' )
        );
    }


}
