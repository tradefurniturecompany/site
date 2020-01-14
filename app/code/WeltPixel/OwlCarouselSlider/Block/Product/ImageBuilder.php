<?php
namespace WeltPixel\OwlCarouselSlider\Block\Product;

use Magento\Catalog\Helper\ImageFactory as HelperFactory;

class ImageBuilder extends \Magento\Catalog\Block\Product\ImageBuilder
{
    /**
     * @var \WeltPixel\OwlCarouselSlider\Helper\Custom
     */
    protected $_helperCustom;

    /**
     * @param HelperFactory $helperFactory
     * @param \Magento\Catalog\Block\Product\ImageFactory $imageFactory
     * @param \WeltPixel\OwlCarouselSlider\Helper\Custom $_helperCustom
     */
    public function __construct(
        HelperFactory $helperFactory,
        \Magento\Catalog\Block\Product\ImageFactory $imageFactory,
        \WeltPixel\OwlCarouselSlider\Helper\Custom $_helperCustom
    ) {
        $this->_helperCustom = $_helperCustom;
        parent::__construct($helperFactory, $imageFactory);
    }



    /**
	 * 2019-10-06 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
	 * «Declaration of WeltPixel\OwlCarouselSlider\Block\Product\ImageBuilder::create()
	 * should be compatible with Magento\Catalog\Block\Product\ImageBuilder::create» in Magento 2.3.2:
	 * https://github.com/tradefurniturecompany/core/issues/7
	 *
     * Create image block
     *
     * @return \Magento\Catalog\Block\Product\Image
     */
	function create(\Magento\Catalog\Model\Product $product = null, string $imageId = null, array $attributes = null)
    {
        /** Check if module is enabled */
        if (!$this->isLazyLoadEnabled()) {
            return parent::create($product, $imageId, $attributes);
        }

        /** @var \Magento\Catalog\Helper\Image $helper */
        $helper = $this->helperFactory->create()
            ->init($this->product, $this->imageId);

        $template = $helper->getFrame()
            ? 'WeltPixel_OwlCarouselSlider::product/image.phtml'
            : 'WeltPixel_OwlCarouselSlider::product/image_with_borders.phtml';

        $data['data']['template'] = $template;

        $imagesize = $helper->getResizedImageInfo();

        $data = [
            'data' => [
                'template' => $template,
                'image_url' => $helper->getUrl(),
                'width' => $helper->getWidth(),
                'height' => $helper->getHeight(),
                'label' => $helper->getLabel(),
                'ratio' =>  $this->getRatio($helper),
                'custom_attributes' => $this->getCustomAttributes(),
                'resized_image_width' => !empty($imagesize[0]) ? $imagesize[0] : $helper->getWidth(),
                'resized_image_height' => !empty($imagesize[1]) ? $imagesize[1] : $helper->getHeight(),
            ],
        ];

        $data['data']['lazy_load'] = true;

        return $this->imageFactory->create($data);
    }

    /**
     * @return bool
     */
    protected function isLazyLoadEnabled() {
        foreach ($this->attributes as $name => $value) {
            if ($name == 'weltpixel_lazyLoad') {
                return true;
            }
        }

        return false;
    }

    /**
     * Retrieve image custom attributes for HTML element
     *
     * @return string
     */
    protected function getCustomAttributes()
    {
        $result = [];
        foreach ($this->attributes as $name => $value) {
            if ($name == 'weltpixel_lazyLoad') continue;
            $result[] = $name . '="' . $value . '"';
        }
        return !empty($result) ? implode(' ', $result) : '';
    }

}