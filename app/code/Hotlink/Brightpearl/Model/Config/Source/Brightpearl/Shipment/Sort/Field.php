<?php
namespace Hotlink\Brightpearl\Model\Config\Source\Brightpearl\Shipment\Sort;

class Field implements \Magento\Framework\Option\ArrayInterface
{
    const GOODS_OUT_NOTE_ID  = "goodsOutNoteId";
    const ORDER_ID           = "orderId";
    const CREATED_ON         = "createdOn";
    const RELEASE_DATE       = "releaseDate";
    const SHIPPING_METHOD_ID = "shippingMethodId";
    const WAREHOUSE_ID       = "warehouseId";
    const CHANNEL_ID         = "channelId";


    public function toOptionArray()
    {
        return [
            [ 'value' => self::CREATED_ON,         'label' => __( 'Created on' ) ],
            [ 'value' => self::RELEASE_DATE,       'label' => __( 'Release date') ],
            [ 'value' => self::GOODS_OUT_NOTE_ID,  'label' => __( 'Goods-out note id' ) ],
            [ 'value' => self::ORDER_ID,           'label' => __( 'Order id' ) ],
            [ 'value' => self::SHIPPING_METHOD_ID, 'label' => __( 'Shipping method id' ) ],
            [ 'value' => self::WAREHOUSE_ID,       'label' => __( 'Warehouse id' ) ],
            [ 'value' => self::CHANNEL_ID,         'label' => __( 'Channel id' ) ] ];
    }

    public function toArray()
    {
        return [
            self::GOODS_OUT_NOTE_ID  => __( 'Goods-out note id' ),
            self::ORDER_ID           => __( 'Order id' ),
            self::CREATED_ON         => __( 'Created on' ),
            self::RELEASE_DATE       => __( 'Release date' ),
            self::SHIPPING_METHOD_ID => __( 'Shipping method id' ),
            self::WAREHOUSE_ID       => __( 'Warehouse id' ),
            self::CHANNEL_ID         => __( 'Channel id' )
            ];
    }
}
