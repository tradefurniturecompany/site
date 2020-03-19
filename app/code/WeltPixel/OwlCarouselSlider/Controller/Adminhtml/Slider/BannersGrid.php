<?php

namespace WeltPixel\OwlCarouselSlider\Controller\Adminhtml\Slider;

/**
 * Banners Grid action
 * @category WeltPixel
 * @package  WeltPixel_OwlCarouselSlider
 * @module   OwlCarouselSlider
 * @author   WeltPixel Developer
 */
class BannersGrid extends \WeltPixel\OwlCarouselSlider\Controller\Adminhtml\Slider
{
    /**
     * Dispatch request
     */
    function execute()
    {
        $resultLayout = $this->_resultLayoutFactory->create();
        
        $resultLayout
            ->getLayout()->getBlock('owlcarouselslider.slider.edit.tab.banners')
            ->setInBanner($this->getRequest()->getPost('banner', null));

        return $resultLayout;
    }
}
