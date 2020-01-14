<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Model\DataProvider;

use Magento\Framework\App\ResourceConnection;
use MageWorx\SeoXTemplates\Model\ConverterCategoryFilterFactory;

class CategoryFilter extends \MageWorx\SeoXTemplates\Model\DataProvider
{
    /**
     * @var ConverterCategoryFactory
     */
    protected $converterCategoryFilterFactory;

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var Resource
     */
    protected $_resource;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $_connection;

    /**
     * CategoryFilter constructor.
     * @param ResourceConnection $resource
     * @param ConverterCategoryFilterFactory $converterCategoryFilterFactory
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceConnection $resource,
        ConverterCategoryFilterFactory $converterCategoryFilterFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct($resource);
        $this->converterCategoryFilterFactory  = $converterCategoryFilterFactory;
        $this->categoryFactory = $categoryFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * Retrieve data
     *
     * @param \Magento\Framework\Data\Collection $collection
     * @param \MageWorx\SeoXTemplates\Model\AbstractTemplate $template
     * @param int|null $customStoreId
     * @return array
     */
    public function getData($collection, $template, $customStoreId = null)
    {
        $data = [];

        $targetPropertyList = $template->getAttributeCodesByType();
        $targetProperty     = $targetPropertyList[0];

        /** @var \MageWorx\SeoExtended\Model\CategoryFilter $categoryFilter */
        foreach ($collection as $categoryFilter) {

            /** @var \Magento\Catalog\Model\Category $category */
            $category = $this->categoryFactory->create();
            $category->setStoreId($categoryFilter->getStoreId());
            $category->load($categoryFilter->getCategoryId());
            $category->setAttributeId($categoryFilter->getAttributeId());
            $categoryName = $category->getName();

            $converter = $this->converterCategoryFilterFactory->create($template->getTypeId());
            $attributeValue = $converter->convert($category, $template->getCode());

            $data[$categoryFilter->getId()] = [
                'filter_id'              => $categoryFilter->getId(),
                'attribute_id'           => $categoryFilter->getAttributeId(),
                'attribute_name'         => $categoryFilter->getAttributeLabel(),
                'attribute_option_id'    => $categoryFilter->getAttributeOptionId(),
                'attribute_option_label' => $categoryFilter->getAttributeOptionLabel(),
                'category_id'            => $categoryFilter->getCategoryId(),
                'category_name'          => $categoryName,
                'category_seo_name'      => $categoryFilter->getCategoryName(),
                'store_id'               => $categoryFilter->getStoreId(),
                'store_name'             => $this->storeManager->getStore($categoryFilter->getStoreId())->getName(),
                'target_property'        => $targetProperty,
                'old_value'              => $categoryFilter->getData($template->getCode()),
                'value'                  => $attributeValue
            ];
        }

        return $data;
    }
}
