<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoXTemplates\Model\DbWriter;

use Magento\Catalog\Model\ResourceModel\Category as CategoryResource;
use Magento\Framework\App\ResourceConnection;
use MageWorx\SeoXTemplates\Model\DataProviderCategoryFilterFactory;
use MageWorx\SeoAll\Helper\LinkFieldResolver;

class CategoryFilter extends \MageWorx\SeoXTemplates\Model\DbWriter
{
    /**
     * Write to database converted string from template code
     *
     * @param \Magento\Catalog\Model\ResourceModel\Category\Collection $collection
     * @param \MageWorx\SeoXTemplates\Model\AbstractTemplate $template
     * @param int $customStoreId
     * @return array|false
     */

    /**
     * @var DataProviderCategoryFilterFactory
     */
    protected $dataProviderCategoryFilterFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category
     */
    protected $categoryResource;

    /**
     * @var \MageWorx\SeoAll\Helper\LinkFieldResolver
     */
    protected $linkFieldResolver;

    /**
     * Eav constructor.
     * @param ResourceConnection $resource
     * @param DataProviderCategoryFilterFactory $dataProviderCategoryFilterFactory
     * @param CategoryResource $categoryResource
     * @param LinkFieldResolver $linkFieldResolver
     */
    public function __construct(
        ResourceConnection $resource,
        DataProviderCategoryFilterFactory $dataProviderCategoryFilterFactory,
        CategoryResource $categoryResource,
        LinkFieldResolver $linkFieldResolver
    ) {
        parent::__construct($resource);
        $this->dataProviderCategoryFilterFactory = $dataProviderCategoryFilterFactory;
        $this->categoryResource = $categoryResource;
        $this->linkFieldResolver = $linkFieldResolver;
    }

    /**
     * @param \MageWorx\SeoExtended\Model\ResourceModel\CategoryFilter\Collection $collection
     * @param \MageWorx\SeoXTemplates\Model\AbstractTemplate $template
     * @param null|int $customStoreId
     * @return bool
     */
    public function write($collection, $template, $customStoreId = null)
    {
        if (!$collection) {
            return false;
        }

        $dataProvider = $this->dataProviderCategoryFilterFactory->create($template->getTypeId());
        $data         = $dataProvider->getData($collection, $template, $customStoreId);

        /** @var \MageWorx\SeoExtended\Model\CategoryFilter $categoryFilter */
        foreach ($collection as $categoryFilter) {
            if (empty($data[$categoryFilter->getId()])) {
                continue;
            }

            $filterData = $data[$categoryFilter->getId()];

            if (!$filterData['value']) {
                continue;
            }

            $categoryFilter->setData($filterData['target_property'], $filterData['value']);
            $categoryFilter->save();
        }

        return true;
    }
}
