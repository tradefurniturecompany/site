<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoMarkup\Helper\DataProvider;

class Category extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     *
     * @var \MageWorx\SeoMarkup\Helper\Category
     */
    protected $helperData;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     *
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category
     */
    protected $resourceCategory;

    /**
     *
     * @var array
     */
    protected $attributeValues = [];

    /**
     *
     * @param \MageWorx\SeoMarkup\Helper\Category $helperData
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Catalog\Model\ResourceModel\Category $resourceCategory
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \MageWorx\SeoMarkup\Helper\Category $helperData,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\ResourceModel\Category $resourceCategory,
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->helperData         = $helperData;
        $this->storeManager       = $storeManager;
        $this->registry           = $registry;
        $this->resourceCategory   = $resourceCategory;
        parent::__construct($context);
    }
}
