<?php
namespace Hotlink\Framework\Model\Config\Field\Order\Status\Sort;

class Source
{

    function toOptionArray()
    {
        return [
            [ 'label' => __( 'Entity Id' ),    'value' => 'entity_id'    ],
            [ 'label' => __( 'Customer Id' ),  'value' => 'customer_id'  ],
            [ 'label' => __( 'Increment Id' ), 'value' => 'increment_id' ],
            [ 'label' => __( 'Date Created' ), 'value' => 'created_at'   ],
            [ 'label' => __( 'Date Updated' ), 'value' => 'updated_at'   ]
        ];
    }

}
