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
	function __construct(
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
	function getProduct()
	{
		if (!$this->_product) {
			$this->_product = $this->_coreRegistry->registry('product');
		}
		return $this->_product;
	}

	function getPaddingBottomPercentage()
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

	/**
	 * @used-by app/code/Rcreek/YoutubeVideo/view/frontend/templates/icon.phtml
	 * @return string
	 */
	function getFirstFrameUrl() {
		$product = $this->getProduct();
		$html = $product->getYoutubeVideo();
		// 2020-02-17 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
		// 1) «Undefined offset: 1 in app/code/Rcreek/YoutubeVideo/Block/YoutubeVideo.php on line 82»:
		// https://github.com/tradefurniturecompany/site/issues/47
		// 2) The previous regular expression was: ~src=".*/([a-zA-Z0-9]*)"~
		// It does not match the cases with the `-` character in the video ID, e.g.:
		// https://www.youtube.com/embed/1iYEQ-tLaXo
		preg_match('~src=".*/([a-zA-Z0-9\-]*)"~', $html, $matches);
		/**
		 * 2020-02-17 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
		 * «Undefined offset: 1 in app/code/Rcreek/YoutubeVideo/Block/YoutubeVideo.php on line 82»:
		 * https://github.com/tradefurniturecompany/site/issues/47
		 */
		if (!($r = !($m = dfa($matches, 1)) ? null : "https://img.youtube.com/vi/$m/0.jpg")) {
			df_log_l($this, $html);
		}
		return $r;
	}

	function getEmbeddedHtml()
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
