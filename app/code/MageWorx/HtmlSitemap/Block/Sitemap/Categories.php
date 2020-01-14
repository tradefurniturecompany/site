<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace MageWorx\HtmlSitemap\Block\Sitemap;

use Magento\Framework\View\Element\Template\Context;
use MageWorx\HtmlSitemap\Model\ResourceModel\Catalog\CategoryFactory;
use MageWorx\HtmlSitemap\Helper\Data as SitemapHelper;

/**
 * Categories block for the recursive catalog output
 */
class Categories extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \MageWorx\HtmlSitemap\Model\ResourceModel\Catalog\CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var \MageWorx\HtmlSitemap\Helper\Data
     */
    protected $sitemapHelper;

    /**
     * @var array
     */
    protected $categories = [];

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param \MageWorx\HtmlSitemap\Helper\Data $sitemapHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        CategoryFactory $categoryFactory,
        SitemapHelper $sitemapHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->categoryFactory = $categoryFactory;
        $this->sitemapHelper = $sitemapHelper;
    }

    /**
     * Retrieve categories tree
     *
     * @return array
     * @see \MageWorx\HtmlSitemap\Model\ResourceModel\Catalog\Category
     */
    public function getTreeCategoryCollection()
    {
        /** @var array **/
        $collection = $this->categoryFactory->create()->getCollection($this->_storeManager->getStore()->getId());

        if (!empty($collection)) {
            foreach ($collection as $item) {
                if (!isset($level)) {
                    $level = $item->getLevel();
                }
                if ($item->getLevel() == $level) {
                    $this->categories[] = $item;
                    $this->addChildren($item->getId(), $collection);
                }
            }
        }
        return $this->categories;
    }

    /**
     * Convert categories to tree
     *
     * @param int $parentId
     * @param array $collection
     */
    protected function addChildren($parentId, $collection)
    {
        foreach ($collection as $item) {
            if ($item->getParentId() != $parentId) {
                continue;
            }
            $this->categories[] = $item;
            if ($item->getChildrenCount()) {
                $this->addChildren($item->getId(), $collection);
            }
        }
    }

    /**
     * Retrieve level
     *
     * @param \Magento\Framework\Object $item
     * @param int $delta
     * @return int
     */
    public function getLevel($item, $delta = 1)
    {
        $this->_storeRootCategoryLevel = 1;
        return (int)($item->getLevel() - $this->_storeRootCategoryLevel - 1) * $delta;
    }

    /**
     * Retrieve config data helper
     *
     * @return \MageWorx\HtmlSitemap\Helper\Data
     */
    public function getSitemapHelper()
    {
        return $this->sitemapHelper;
    }

    /**
     * Check if product show by config setting and category property
     *
     * @param \Magento\Framework\Object $category
     * @return boolean
     */
    public function isShowProducts($category)
    {
        if (!$this->sitemapHelper->isShowProducts()) {
            return false;
        }
        if ($this->sitemapHelper->isUseCategoryDisplayMode()) {
            if ($category->getDisplayMode() == \Magento\Catalog\Model\Category::DM_PAGE) {
                return false;
            }
        }
        return true;
    }
}
