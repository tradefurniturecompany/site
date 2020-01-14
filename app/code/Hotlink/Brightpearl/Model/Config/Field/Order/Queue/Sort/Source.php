<?php
namespace Hotlink\Brightpearl\Model\Config\Field\Order\Queue\Sort;

class Source extends \Hotlink\Framework\Model\Config\Field\Order\Status\Sort\Source
{

    public function toOptionArray()
    {
        $options = parent::toOptionArray();

        $options[] = array( 'label' => __( 'Date Queued' ),
                            'value' => 'queued_at' );

        return $options;
    }

}
