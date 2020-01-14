<?php

namespace IWD\InfinityScroll\Block\Frontend;

use Magento\Framework\View\Element\Template;
use Magento\Framework\Registry;
use IWD\InfinityScroll\Model\Config\Source\Mode;

/**
 * Class Init
 * @package IWD\InfinityScroll\Block\Frontend
 */
class Init extends Template
{
    const XPATH_ENABLED = 'infinity_scroll/general/enable';
    const XPATH_ENABLED_ON_CATALOG = 'infinity_scroll/general/enable_on_catalog';
    const XPATH_ENABLED_ON_SEARCH = 'infinity_scroll/general/enable_on_search';
    const XPATH_ENABLED_ON_ADVANCED_SEARCH = 'infinity_scroll/general/enable_on_advanced_search';

    const XPATH_MODE = 'infinity_scroll/general/mode';

    const XPATH_SELECTOR_CONTENT = 'infinity_scroll/additional/selector_content';
    const XPATH_SELECTOR_LAYERED = 'infinity_scroll/additional/selector_layered';
    const XPATH_SELECTOR_TOOLBAR = 'infinity_scroll/additional/selector_toolbar';
    const XPATH_SELECTOR_LIMITER = 'infinity_scroll/additional/selector_limiter';
    const XPATH_SELECTOR_PAGINATION = 'infinity_scroll/additional/selector_pagination';
    const XPATH_SELECTOR_AMOUNT = 'infinity_scroll/additional/selector_amount';

    const XPATH_LOAD_NEXT_BUTTON = 'infinity_scroll/design/load_next_page_label';
    const XPATH_LOAD_PREV_BUTTON = 'infinity_scroll/design/load_prev_page_label';
    const XPATH_IS_BLOCK_HEADER = 'infinity_scroll/design/block_header';

    /**
     * @var Registry
     */
    private $registry;

    /**
     * Init constructor.
     * @param Template\Context $context
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Registry $registry,
        array $data
    ) {
        $this->registry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->_scopeConfig->getValue(self::XPATH_ENABLED);
    }

    /**
     * @return bool
     */
    public function isButtonMode()
    {
        return $this->getUploadMode() == Mode::MODE_BUTTON;
    }

    /**
     * @return bool
     */
    public function isPagination()
    {
        return $this->getUploadMode() == Mode::MODE_PAGINATION;
    }

    /**
     * @return bool
     */
    public function isPageBlockHeader()
    {
        return $this->_scopeConfig->getValue(self::XPATH_IS_BLOCK_HEADER);
    }

    /**
     * @return bool
     */
    public function getUploadMode()
    {
        return $this->_scopeConfig->getValue(self::XPATH_MODE);
    }

    /**
     * @return string
     */
    public function getLoadNextPageButtonLabel()
    {
        return $this->_scopeConfig->getValue(self::XPATH_LOAD_NEXT_BUTTON);
    }

    /**
     * @return string
     */
    public function getLoadPrevPageButtonLabel()
    {
        return $this->_scopeConfig->getValue(self::XPATH_LOAD_PREV_BUTTON);
    }

    /**
     * @return int
     */
    public function getCountPages()
    {
        return $this->registry->registry('catalogCollectionLastPageNumber');
    }
    /**
     * @return int
     */
    public function getCurPage()
    {
        return $this->registry->registry('catalogCollectionCurPage');
    }

    /**
     * @return string
     */
    public function getXpathSelectorContent()
    {
        return $this->_scopeConfig->getValue(self::XPATH_SELECTOR_TOOLBAR);
    }

    /**
     * @return string
     */
    public function getXpathSelectorLimiter()
    {
        return $this->_scopeConfig->getValue(self::XPATH_SELECTOR_LIMITER);
    }

    /**
     * @return string
     */
    public function getXpathSelectorPagination()
    {
        return $this->_scopeConfig->getValue(self::XPATH_SELECTOR_PAGINATION);
    }

    /**
     * @return string
     */
    public function getXpathSelectorAmount()
    {
        return $this->_scopeConfig->getValue(self::XPATH_SELECTOR_AMOUNT);
    }

    /**
     * @return string
     */
    public function getOptionsJson()
    {
        return json_encode([
            'pagesCount' => $this->getCountPages(),
            'uploadMode' => $this->getUploadMode(),
            'pageBlockHeader' => (bool)$this->isPageBlockHeader(),
            'selectorContent' => $this->_scopeConfig->getValue(self::XPATH_SELECTOR_CONTENT),
            'selectorLayered' => $this->_scopeConfig->getValue(self::XPATH_SELECTOR_LAYERED),
            'selectorToolbar' => $this->getXpathSelectorContent(),
            'selectorToolbarLimiter' => $this->getXpathSelectorLimiter(),
            'selectorToolbarPages' => $this->getXpathSelectorPagination(),
            'selectorToolbarAmount' => $this->getXpathSelectorAmount(),
        ]);
    }
}
