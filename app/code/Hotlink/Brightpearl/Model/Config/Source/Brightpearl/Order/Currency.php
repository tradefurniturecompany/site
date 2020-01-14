<?php
namespace Hotlink\Brightpearl\Model\Config\Source\Brightpearl\Order;

class Currency implements \Magento\Framework\Option\ArrayInterface
{
    const BASE               = 'base';              // use store base currency
    const ORDER              = 'order';             // use order currency

    public function toOptionArray()
    {
        return [
            [ 'value' => self::BASE,
              'label' => __( 'Base Currency' ) ],
            [ 'value' => self::ORDER,
              'label' => __( 'Order Currency' ) ] ];
    }

    public function toArray()
    {
        return [
            self::BASE  => __( 'Base Currency' ),
            self::ORDER => __( 'Order Currency' )
            ];
    }
}
