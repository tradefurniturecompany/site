<?php
namespace WeltPixel\OwlCarouselSlider\Block\Slider;

class Custom extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{
    protected $_sliderId;
    protected $_sliderConfiguration;
    protected $_helperCustom;
    protected $_filterProvider;

    /**
     * Custom constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \WeltPixel\OwlCarouselSlider\Helper\Custom $helperCustom
     * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
     * @param array $data
     */

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \WeltPixel\OwlCarouselSlider\Helper\Custom $helperCustom,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,

        array $data = []
    )
    {
        $this->_coreRegistry = $registry;
        $this->_helperCustom = $helperCustom;
        $this->_filterProvider = $filterProvider;

        $this->setTemplate('sliders/custom.phtml');

        parent::__construct($context, $data);
    }

    /**
     * @param $video
     * @return mixed
     */
    public function getVideoHtml($video){
        $storeId = $this->_storeManager->getStore()->getId();
        return $this->_filterProvider->getBlockFilter()->setStoreId($storeId)->filter($video);
    }

    public function getSliderConfiguration()
    {
        $sliderId = $this->getData('slider_id');

        if ($this->_sliderId != $sliderId) {
            $this->_sliderId = $sliderId;
        }

        if (is_null($this->_sliderConfiguration)) {

            $this->_sliderConfiguration = $this->_helperCustom->getSliderConfigOptions($this->_sliderId);
        }

        return $this->_sliderConfiguration;
    }

    public function getBreakpointConfiguration()
    {
        return $this->_helperCustom->getBreakpointConfiguration();
    }

    public function getMediaUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

    public function isGatEnabled()
    {
        return $this->_helperCustom->isGatEnabled();
    }
}
