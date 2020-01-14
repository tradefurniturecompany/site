<?php
/**
 * Copyright © 2015 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoBase\Model\NextPrev;

use MageWorx\SeoBase\Helper\Data as HelperData;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use MageWorx\SeoBase\Helper\Url as HelperUrl;
use Magento\Framework\View\Layout;

/**
 * SEO Base category next/prev model
 */
class Category extends \MageWorx\SeoBase\Model\NextPrev
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var string
     */
    protected $prevUrl = null;

    /**
     * @var string
     */
    protected $nextUrl = null;

    /**
     * @var bool
     */
    protected $initFlag;

    /**
     *
     * @param HelperData $helperData
     * @param HelperUrl $helperUrl
     * @param Registry $registry
     * @param UrlInterface $url
     * @param Layout $layout
     */
    public function __construct(
        HelperData $helperData,
        HelperUrl $helperUrl,
        Registry $registry,
        UrlInterface $url,
        Layout $layout
    ) {

        $this->registry = $registry;
        $this->layout = $layout;
        $this->url = $url;
        parent::__construct($helperData, $helperUrl);
    }

    /**
     * Retrieve next page URL
     *
     * @return string
     */
    public function getNextUrl()
    {
        return $this->init()->nextUrl;
    }

    /**
     * Retrieve previous page URL
     *
     * @return string
     */
    public function getPrevUrl()
    {
        return $this->init()->prevUrl;
    }

    /**
     * Retrieve pager block from layout
     *
     * @return \Magento\Theme\Block\Html\Pager
     */
    protected function getPager()
    {
        if (is_object($this->layout)) {
            return $this->layout->getBlock('product_list_toolbar_pager');
        }
    }

    /**
     * Initialize
     *
     * @return this
     */
    protected function init()
    {
        if ($this->initFlag) {
            return $this;
        }
        $pager = $this->getPager();
        if (!is_object($pager)) {
            $this->initFlag = true;
            return $this;
        }

        if (!$pager->getCollection()) {
            $this->initFlag = true;
            return $this;
        }

        if ($pager->getLastPageNum() > 1) {
            if (!$pager->isLastPage()) {
                $this->nextUrl  = $pager->getNextPageUrl();
            }

            $pageVarName = $pager->getPageVarName();
            if ($pager->getCurrentPage() == 2) {
                $this->prevUrl = $this->removeFirstPage($pager->getPreviousPageUrl(), $pageVarName);
            } elseif ($pager->getCurrentPage() > 2) {
                $this->prevUrl = $pager->getPreviousPageUrl();
            }
        }
        $this->initFlag = true;
        return $this;
    }
}
