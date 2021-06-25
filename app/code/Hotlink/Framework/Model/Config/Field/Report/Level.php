<?php
namespace Hotlink\Framework\Model\Config\Field\Report;


class Level implements \Magento\Framework\Option\ArrayInterface
{
    function toOptionArray($isMultiselect = false)
    {
        $options = [
            [ 'value' => \Hotlink\Framework\Model\Report\Item::LEVEL_FATAL, 'label' => 'FTL' ],
            [ 'value' => \Hotlink\Framework\Model\Report\Item::LEVEL_ERROR, 'label' => 'ERR' ]  ,
            [ 'value' => \Hotlink\Framework\Model\Report\Item::LEVEL_WARN,  'label' => 'WRN' ],
            [ 'value' => \Hotlink\Framework\Model\Report\Item::LEVEL_INFO,  'label' => 'INF' ],
            [ 'value' => \Hotlink\Framework\Model\Report\Item::LEVEL_DEBUG, 'label' => 'DBG' ],
            [ 'value' => \Hotlink\Framework\Model\Report\Item::LEVEL_TRACE, 'label' => 'TRC' ],
            ];

        if ( ! $isMultiselect ) {
            array_unshift( $options, [ 'value' => '', 'label' => '' ] );
        }

        return $options;
    }

    function toArray()
    {
        return  [
            \Hotlink\Framework\Model\Report\Item::LEVEL_FATAL => 'FTL',
            \Hotlink\Framework\Model\Report\Item::LEVEL_ERROR => 'ERR',
            \Hotlink\Framework\Model\Report\Item::LEVEL_WARN  => 'WRN',
            \Hotlink\Framework\Model\Report\Item::LEVEL_INFO  => 'INF',
            \Hotlink\Framework\Model\Report\Item::LEVEL_DEBUG => 'DBG',
            \Hotlink\Framework\Model\Report\Item::LEVEL_TRACE => 'TRC'
            ];
    }
}
