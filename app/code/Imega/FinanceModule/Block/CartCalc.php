<?php

namespace Imega\FinanceModule\Block;

use Imega\FinanceModule\Helper\Data;
use Imega\FinanceModule\Helper\CartData;
use \Magento\Framework\View\Element\Template;

class CartCalc extends Template
{
    const FINANCE_FILTER_ALL = 'ALL';
    const FINANCE_FILTER_EXCLUDE = 'exclude';
    const MULTI_PRODUCT_ITEMS = ['bundle'];

    protected $config;
    protected $cartConfig;
    protected $checkoutSession;

    public $widgetIsEnabled;
    public $apiKey;
    public $element;
    public $priceElement;
    public $priceElementInner;
    public $anchorMargin;
    public $anchorWidth;
    public $anchorPosition;
    public $hideIfNotInRange;

    public function __construct(
        CartData $cartConfig,
        Data $config,
        Template\Context $context,
        array $data = [],
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        $this->cartConfig = $cartConfig;
        $this->config = $config;
        $this->checkoutSession = $checkoutSession;
        $this->widgetIsEnabled = $this->cartConfig->widgetIsEnabled();
        $this->apiKey = $config->getApiKey();
        $this->element = $cartConfig->getPositionSelector();
        $this->priceElement = $cartConfig->getPriceElement();
        $this->priceElementInner = $cartConfig->getInnerPriceElement();
        $this->anchorMargin = $cartConfig->getMargin();
        $this->anchorWidth = $cartConfig->getWidth();
        $this->anchorPosition = $cartConfig->getPosition();
        $this->hideIfNotInRange = $cartConfig->hideIfNotInRange();
        parent::__construct($context, $data);
    }

    public function getTotal()
    {

        if ($this->checkoutSession->getQuote()->isVirtual()) {
          $quote = $this->checkoutSession->getQuote()->getBillingAddress()->getTotals();
        } else {
          $quote = $this->checkoutSession->getQuote()->getShippingAddress()->getTotals();
        }
        $total = $quote['grand_total']->getData('value');
        return $total;
    }

    public function getDescription()
    {
        $items = $this->checkoutSession->getQuote()->getAllVisibleItems();
        $descString = '';
        foreach ($items as $item) {
            $descString.= htmlspecialchars($item->getName(), ENT_QUOTES).'|||';
        }
        return $descString;
    }

    public function getFinanceFilter()
    {
        $items = $this->checkoutSession->getQuote()->getAllVisibleItems();
        $productKeywords =[];
        foreach ($items as $item) {
            if (in_array($item->getProductType(),self::MULTI_PRODUCT_ITEMS)){
              foreach($item->getChildren() as $childItem){
                array_push($productKeywords, $childItem->getProduct()->getImegamediaFinanceFilter());
              }
            } else {
              array_push($productKeywords, $item->getProduct()->getImegamediaFinanceFilter());
            }
        }
        if(in_array( self::FINANCE_FILTER_EXCLUDE, array_map('strtolower', $productKeywords))){
          return self::FINANCE_FILTER_EXCLUDE;
        }

        $keywords = array_unique($productKeywords);
        if (count($keywords) === 1 && !empty($keywords[0])) {
            return $keywords[0];
        } else {
            return self::FINANCE_FILTER_ALL;
        }
    }
}
