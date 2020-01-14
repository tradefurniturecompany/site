<?php

namespace WeltPixel\OwlCarouselSlider\Block\Adminhtml\Banner\Helper\Renderer;

/**
 * Image renderer.
 * @category WeltPixel
 * @package  WeltPixel_OwlCarouselSlider
 * @module   OwlCarouselSlider
 * @author   WeltPixel Developer
 */
class MobileImage extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Render action.
     *
     * @param \Magento\Framework\DataObject $row
     *
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        return '<a target="_blank" href="https://www.weltpixel.com/owl-carousel-and-slider.html">Upgrade to Pro version</a><br/>to enable this functionality.';
    }
}
