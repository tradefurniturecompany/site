<?php
namespace Hotlink\Brightpearl\Model\Platform\Brightpearl;

class Events
{
    // DROP-SHIP NOTE
    const DROP_SHIP_NOTE = 'drop-ship-note';
    const DROP_SHIP_NOTE_CREATED = 'drop-ship-note.created';
    const DROP_SHIP_NOTE_DESTROYED = 'drop-ship-note.destroyed';
    const DROP_SHIP_NOTE_MODIFIED_SHIPPED = 'drop-ship-note.modified.shipped';

    // GOODS-IN NOTE
    const GOODS_IN_NOTE_CREATED = 'goods-in-note.created';

    // GOODS-OUT NOTE
    const GOODS_OUT_NOTE = 'goods-out-note';
    const GOODS_OUT_NOTE_CREATED = 'goods-out-note.created';
    const GOODS_OUT_NOTE_DESTROYED = 'goods-out-note.destroyed';
    const GOODS_OUT_NOTE_MODIFIED_PRINTED = 'goods-out-note.modified.printed';
    const GOODS_OUT_NOTE_MODIFIED_PICKED = 'goods-out-note.modified.picked';
    const GOODS_OUT_NOTE_MODIFIED_PACKED = 'goods-out-note.modified.packed';
    const GOODS_OUT_NOTE_MODIFIED_UN_PRINTED = 'goods-out-note.modified.un-printed';
    const GOODS_OUT_NOTE_MODIFIED_UN_PICKED = 'goods-out-note.modified.un-picked';
    const GOODS_OUT_NOTE_MODIFIED_UN_PACKED = 'goods-out-note.modified.un-packed';
    const GOODS_OUT_NOTE_MODIFIED_SHIPPED = 'goods-out-note.modified.shipped';

    // ORDER
    const ORDER_MODIFIED_FULLY_SHIPPED = 'order.modified.fully-shipped';
    const ORDER_MODIFIED_ORDER_STATUS = 'order.modified.order-status';

    // PRODUCT
    const PRODUCT_CREATED = 'product.created';
    const PRODUCT_DESTROYED = 'product.destroyed';
    const PRODUCT_MODIFIED_ON_HAND_MODIFIED = 'product.modified.on-hand-modified';
}
