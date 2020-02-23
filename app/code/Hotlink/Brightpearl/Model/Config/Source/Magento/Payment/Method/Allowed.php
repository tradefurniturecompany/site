<?php
namespace Hotlink\Brightpearl\Model\Config\Source\Magento\Payment\Method;

class Allowed implements \Magento\Framework\Option\ArrayInterface
{

    /**
     * @var \Magento\Payment\Helper\Data
     */
    protected $paymentHelper;

    function __construct(
        \Magento\Payment\Helper\Data $paymentHelper
    ) {
        $this->paymentHelper = $paymentHelper;
    }

    function toOptionArray()
    {
        $ret = $this->paymentHelper->getPaymentMethodList( true, true, false, null );
        if ( $ret )
            {
                foreach ( $ret as &$item )
                    {
                        $item['label'] .= ' [' . $item['value'] .']';
                    }
            }
        return $ret;
    }

    function toArray()
    {
        $ret = $this->paymentHelper->getPaymentMethodList( true, false, false, null );
        if ( $ret )
            {
                foreach ( $ret as $value => $label )
                    {
                        $ret[$value] .= ' [' . $value . ']';
                    }
            }
        return $ret;
    }
}
