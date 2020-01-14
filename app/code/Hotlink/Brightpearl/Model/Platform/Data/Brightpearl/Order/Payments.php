<?php
namespace Hotlink\Brightpearl\Model\Platform\Data\Brightpearl\Order;

class Payments extends \Hotlink\Brightpearl\Model\Platform\Data
{

    protected function _map_object_magento( array $payments )
    {
        foreach ( $payments as $payment )
            {
                if ( $payment instanceof \Magento\Sales\Model\Order\Payment )
                    {
                        $this[] = $this->getObject( $payment, ucfirst($payment->getMethod()) );
                    }
            }
        return $this;
    }

    public function getChildClassDefault( $key )
    {
        $this->getReport()->debug( $this->annotate( "No specific mapper for Payment code $key available, using default" ) );
        return '\Hotlink\Brightpearl\Model\Platform\Data\Brightpearl\Order\Payments\DefaultPayments';
    }

}