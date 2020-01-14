<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoExtended\Model\PageNum;

use MageWorx\SeoExtended\Helper\Data as HelperData;

/**
 * SEO Extended category pagenum model
 */
class Category extends \MageWorx\SeoExtended\Model\PageNum
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     *
     * @var \Magento\Framework\View\Layout
     */
    protected $layout;

    /**
     *
     * @param HelperData $helperData
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\View\Layout $layout
     * @param \Magento\Framework\Registry $registry
     * @param string $pageVarName
     * @param string $fullActionName
     */
    public function __construct(
        HelperData $helperData,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\View\Layout $layout,
        \Magento\Framework\Registry $registry,
        $pageVarName = 'p',
        $fullActionName = null
    ) {
        $this->registry = $registry;
        $this->layout   = $layout;
        parent::__construct($helperData, $request, $fullActionName, $pageVarName);
    }

    /**
     * {@inheritDoc}
     */
    public function getCurrentPageNum()
    {
        return (int)$this->getCategoryPageNum();
    }

    /**
     *
     *
     * @return int
     */
    protected function getCategoryPageNum()
    {
        $pager = $this->getPager();
        if ($pager instanceof Magento\Theme\Block\Html) {
            return (int)$pager->getCurrentPage();
        }

        return (int)$this->getPagerNumFromRequest();
    }

    /**
     * Retrieve pager block from layout
     *
     * @return \Magento\Theme\Block\Html\Pager|null
     */
    protected function getPager()
    {
        if (!is_object($this->layout)) {
            return null;
        }

        $this->layout->getBlock('product_list_toolbar_pager');
    }
}
