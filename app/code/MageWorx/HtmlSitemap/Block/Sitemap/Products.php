<?php
/**
 * Copyright © 2015 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\HtmlSitemap\Block\Sitemap;

use \Magento\Framework\View\Element\Template\Context;
use \MageWorx\HtmlSitemap\Model\ResourceModel\Catalog\ProductFactory;

/**
 * Product block
 */
class Products extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \MageWorx\HtmlSitemap\Model\ResourceModel\Catalog\ProductFactory
     */
    protected $productFactory;

    /**
     * @var int
     */
    protected $categoryId;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \MageWorx\HtmlSitemap\Model\ResourceModel\Catalog\ProductFactory $productFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        ProductFactory $productFactory,
        array $data = []
    ) {
        $this->productFactory = $productFactory;
        parent::__construct($context, $data);
    }

    /**
     *
     * @param int $id
     * @return this
     */
    public function setCategoryId($id)
    {
        $this->categoryId = $id;
        return $this;
    }

    /**
     * Retrieve array of product items (\Magento\Framework\Object) or false
     *
     * @return array|false
     * @see \MageWorx\HtmlSitemap\Model\ResourceModel\Catalog\Product
     */
    public function getProductCollection()
    {
        if (!$this->categoryId) {
            return [];
        }
        $this->getCategory();
        return $this->productFactory->create()->getCollection($this->categoryId, $this->_storeManager->getStore()->getId());
    }
}
