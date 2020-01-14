<?php
/**
 * Venustheme
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Venustheme.com license that is
 * available through the world-wide-web at this URL:
 * http://www.venustheme.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Venustheme
 * @package    Ves_Megamenu
 * @copyright  Copyright (c) 2016 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */
namespace Ves\Megamenu\Block\Widget;

class Products extends \Magento\Catalog\Block\Product\AbstractProduct implements \Magento\Widget\Block\BlockInterface
{
	/**
     * @var \Ves\Megamenu\Model\Product
     */
	protected $_productModel;

	/**
	 * @var \Magento\Catalog\Model\Product
	 */
	protected $_collection;

	protected $httpContext;

	public function __construct(
		\Magento\Catalog\Block\Product\Context $context,
		\Ves\Megamenu\Model\Product $productModel,
		\Magento\Framework\App\Http\Context $httpContext,
		array $data = []
		) {
		$this->_productModel = $productModel;
		$this->httpContext = $httpContext;
		parent::__construct($context, $data );
	}

	protected function _beforeToHtml()
	{
		$catIds = [];
		$categories = $this->getConfig("categories");
		if($categories!=''){
			$catIds = explode(",", $categories);
		}
		// if($this->getConfig("enable_owlcarousel")){
		// 	$this->setTemplate('widget/product_carousel.phtml');
		// }else{
		// 	$this->setTemplate('widget/product_list.phtml');
		// }
		$layoutType = $this->getConfig("layout_type");
		if($layoutType == 'owl_carousel'){
			$this->setTemplate('widget/product_carousel.phtml');
		}elseif($layoutType == 'bootstrap_carousel'){
			$this->setTemplate('widget/bootstrapcarousel.phtml');
		}
		$source_key = $this->getConfig("product_source");
		$config = [];
		$config['pagesize'] = $this->getConfig('number_item',12);
		$config['cats'] = $catIds;
		$collection = $this->_productModel->getProductBySource($source_key, $config);

		$this->_collection = $collection;
		return parent::_beforeToHtml();
	}

	/**
     * Get Key pieces for caching block content
     *
     * @return array
     */
	public function getCacheKeyInfo()
	{
		return [
		'VES_MEGAMENU_PRODUCT',
		$this->_storeManager->getStore()->getId(),
		$this->_design->getDesignTheme()->getId(),
		$this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_GROUP),
		'template' => $this->getTemplate(),
		$this->getConfig("number_item")
		];
	}

	public function getConfig($key, $default = '')
	{
		if($this->hasData($key) && $this->getData($key))
		{
			return $this->getData($key);
		}
		return $default;
	}

	/**
     * Check product is new
     *
     * @param  Mage_Catalog_Model_Product $_product
     * @return bool
     */
	public function checkProductIsNew($_product = null) {
		$from_date = $_product->getNewsFromDate();
		$to_date = $_product->getNewsToDate();
		$is_new = false;
		$is_new = $this->isNewProduct($from_date, $to_date);
		$today = strtotime("now");

		if ($from_date && $to_date) {
			$from_date = strtotime($from_date);
			$to_date = strtotime($to_date);
			if ($from_date <= $today && $to_date >= $today) {
				$is_new = true;
			}
		}
		elseif ($from_date && !$to_date) {
			$from_date = strtotime($from_date);
			if ($from_date <= $today) {
				$is_new = true;
			}
		}elseif (!$from_date && $to_date) {
			$to_date = strtotime($to_date);
			if ($to_date >= $today) {
				$is_new = true;
			}
		}
		return $is_new;
	}

	public function getProductCollection(){
		return $this->_collection;
	}

	public function isNewProduct( $created_date, $num_days_new = 3) {
		$check = false;

		$startTimeStamp = strtotime($created_date);
		$endTimeStamp = strtotime("now");

		$timeDiff = abs($endTimeStamp - $startTimeStamp);
        $numberDays = $timeDiff/86400;// 86400 seconds in one day

        // and you might want to convert to integer
        $numberDays = intval($numberDays);
        if($numberDays <= $num_days_new) {
        	$check = true;
        }

        return $check;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function getVesProductPriceHtml(
    	\Magento\Catalog\Model\Product $product,
    	$priceType = null,
    	$renderZone = \Magento\Framework\Pricing\Render::ZONE_ITEM_LIST,
    	array $arguments = []
    	) {
    	if (!isset($arguments['zone'])) {
    		$arguments['zone'] = $renderZone;
    	}
    	$arguments['price_id'] = isset($arguments['price_id'])
    	? $arguments['price_id']
    	: 'old-price-' . $product->getId() . '-' . $priceType;
    	$arguments['include_container'] = isset($arguments['include_container'])
    	? $arguments['include_container']
    	: true;
    	$arguments['display_minimal_price'] = isset($arguments['display_minimal_price'])
    	? $arguments['display_minimal_price']
    	: true;
    	$priceRender = $this->getLayout()->getBlock('product.price.render.default');

    	$price = '';
    	if ($priceRender) {
    		$price = $priceRender->render(
    			\Magento\Catalog\Pricing\Price\FinalPrice::PRICE_CODE,
    			$product,
    			$arguments
    			);
    	}
    	return $price;
    }
}