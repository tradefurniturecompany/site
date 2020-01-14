<?php
namespace Hotlink\Brightpearl\Model\Config\Source\Brightpearl\Order\Sort;

class Direction implements \Magento\Framework\Option\ArrayInterface
{

    public function toOptionArray()
    {
        return [
            [ 'value' => "ASC", 'label' => __( 'Ascending' ) ],
            [ 'value' => "DESC", 'label' => __( 'Descending' ) ] ];
    }
    public function toArray()
    {
        return [ "ASC"  => __( 'Ascending' ),
                 "DESC" => __( 'Descending' ) ];
    }
}