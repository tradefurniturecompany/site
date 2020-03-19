<?php
/**
 * Used in creating options for category config value selection
 *
 */
namespace WeltPixel\OwlCarouselSlider\Model\Config\Source;
class BestSellPeriod implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    function toOptionArray()
    {
        return [
            [
                'label' => __('All Time'),
                'value' => 'beginning',
            ]
        ];
    }
    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    function toArray()
    {
        return [
            'beginning' => __('All Time')
        ];
    }
}