<?php
namespace Hotlink\Brightpearl\Model\Config\Source\Magento\Payment\Method;

class Allowed implements \Magento\Framework\Option\ArrayInterface
{

    /**
     * @var \Magento\Payment\Helper\Data
     */
    protected $paymentHelper;

    public function __construct(
        \Magento\Payment\Helper\Data $paymentHelper
    ) {
        $this->paymentHelper = $paymentHelper;
    }

    public function toOptionArray()
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

    public function toArray()
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
