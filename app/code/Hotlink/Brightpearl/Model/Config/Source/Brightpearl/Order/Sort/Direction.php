<?php
namespace Hotlink\Brightpearl\Model\Config\Source\Brightpearl\Order\Sort;

class Direction implements \Magento\Framework\Option\ArrayInterface
{

    function toOptionArray()
    {
        return [
            [ 'value' => "ASC", 'label' => __( 'Ascending' ) ],
            [ 'value' => "DESC", 'label' => __( 'Descending' ) ] ];
    }
    function toArray()
    {
        return [ "ASC"  => __( 'Ascending' ),
                 "DESC" => __( 'Descending' ) ];
    }
}