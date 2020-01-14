<?php

namespace Rcreek\YoutubeVideo\Block;

use Magento\Catalog\Model\Product;

class YoutubeVideo extends \Magento\Framework\View\Element\Template
{

    /**
     * @var Product
     */
    protected $_product = null;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;


    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    )
    {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * @return Product
     */
    public function getProduct()
    {
        if (!$this->_product) {
            $this->_product = $this->_coreRegistry->registry('product');
        }
        return $this->_product;
    }

    public function getPaddingBottomPercentage()
    {
        $product = $this->getProduct();
        $original = $product->getYoutubeVideo();

        if (!$original) {
            return 0;
        }

        // strip height
        preg_match('/height="(\d*)"/', $original, $matches);
        $height = $matches[1];
        if (!filter_var($height, FILTER_VALIDATE_INT)) {
            $height = 360;
        }

        // strip width
        preg_match('/width="(\d*)"/', $original, $matches);
        $width = $matches[1];
        if (!filter_var($width, FILTER_VALIDATE_INT)) {
            $width = 480;
        }

        return (($height / $width) * 100);
    }

    public function getFirstFrameUrl()
    {
        $product = $this->getProduct();
        $html = $product->getYoutubeVideo();
        preg_match('~src=".*/([a-zA-Z0-9]*)"~', $html, $matches);

        $url = 'https://img.youtube.com/vi/';
        $url .= $matches[1];
        $url .= '/0.jpg';

        return $url;
    }

    public function getEmbeddedHtml()
    {
        $product = $this->getProduct();
        $original = $product->getYoutubeVideo();
        // strip height
        $stripped = preg_replace('/height="\d*"/', '', $original);
        // strip width
        $stripped = preg_replace('/width="\d*"/', '', $stripped);

        return $stripped;
    }

}
