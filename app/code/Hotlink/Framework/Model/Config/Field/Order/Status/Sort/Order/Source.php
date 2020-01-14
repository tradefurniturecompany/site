<?php
namespace Hotlink\Framework\Model\Config\Field\Order\Status\Sort\Order;

class Source
{

    public function toOptionArray()
    {
        return [
            [ 'label' => __( 'Ascending' ),  'value' => \Magento\Framework\Data\Collection::SORT_ORDER_ASC  ],
            [ 'label' => __( 'Descending' ), 'value' => \Magento\Framework\Data\Collection::SORT_ORDER_DESC ]
        ];
    }

}
