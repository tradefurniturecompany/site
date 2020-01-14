<?php
namespace Hotlink\Brightpearl\Model\Config\Source\Brightpearl\Shipment;

class Type implements \Magento\Framework\Option\ArrayInterface
{
    const DROP_SHIP = 'drop_ship';
    const GOODS_OUT = 'goods_out';

    public function toOptionArray()
    {
        return [
            [ 'value' => self::GOODS_OUT, 'label' => __( 'Goods Out' ) ],
            [ 'value' => self::DROP_SHIP, 'label' => __( 'Drop Ship' ) ] ];

    }

    public function toArray()
    {
        return [
            self::DROP_SHIP => __( 'Drop Ship' ),
            self::GOODS_OUT => __( 'Goods Out' )
            ];
    }
}
