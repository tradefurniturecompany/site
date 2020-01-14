<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoReports\Model\Trigger;

use MageWorx\SeoReports\Model\GeneratorFactory;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\CatalogUrlRewrite\Model\CategoryUrlRewriteGenerator;


class CategoryReportTrigger
{
    /**
     * @var \MageWorx\SeoReports\Model\Config\Category
     */
    protected $categoryConfig;

    /**
     * @var GeneratorFactory
     */
    protected $generatorFactory;

    /**
     * @var \MageWorx\SeoAll\Model\ResourceModel\Category
     */
    protected $categoryResource;

    /**
     * @var \Magento\UrlRewrite\Model\UrlFinderInterface
     */
    protected $urlFinder;

    /**
     * CategoryReportTrigger constructor.
     *
     * @param \MageWorx\SeoReports\Model\Config\Category $categoryConfig
     * @param GeneratorFactory $generatorFactory
     * @param \MageWorx\SeoAll\Model\ResourceModel\Category $category
     * @param \Magento\UrlRewrite\Model\UrlFinderInterface $urlFinder
     */
    public function __construct(
        \MageWorx\SeoReports\Model\Config\Category $categoryConfig,
        \MageWorx\SeoReports\Model\GeneratorFactory $generatorFactory,
        \MageWorx\SeoAll\Model\ResourceModel\Category $category,
        \Magento\UrlRewrite\Model\UrlFinderInterface $urlFinder
    ) {
        $this->categoryConfig   = $categoryConfig;
        $this->generatorFactory = $generatorFactory;
        $this->categoryResource = $category;
        $this->urlFinder        = $urlFinder;
    }

    /**
     * @param \Magento\Catalog\Model\Category $category
     */
    public function generateReportForNewCategory($category)
    {
        $categoryStoreIds = array_filter($category->getStoreIds());
        $requestPaths     = $this->getCategoryUrlsByStores($category, $categoryStoreIds);

        foreach ($categoryStoreIds as $storeId) {
            $requestPath = !empty($requestPaths[$storeId]) ? $requestPaths[$storeId] : null;
            $category->setData('request_path', $requestPath);
            $category->setStoreId($storeId);
            $this->getGenerator()->generate($category);
        }

        $category->setStoreId(0);
    }

    /**
     * @param \Magento\Catalog\Model\Category $category
     */
    public function generateReportForCategoryOnAllStores($category)
    {
        $fieldsForCheck = $this->getModifiedAttributes($category);

        $attributesStoreValues = [];

        foreach ($fieldsForCheck as $field) {
            /** @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute */
            $attribute = $this->categoryResource->getAttribute($field);

            if ($attribute && !$attribute->getIsGlobal()) {
                $attributesStoreValues[$field] = $this->categoryResource->getAttributeValues(
                    $attribute,
                    $category->getId()
                );
            }
        }

        $dataByStores = [];

        foreach ($attributesStoreValues as $attributeName => $attributeStoreValues) {

            foreach ($category->getStoreIds() as $storeId) {

                //add new values from default scope to attributes which hasn't own values for current store view
                if (!array_key_exists($storeId, $attributeStoreValues)) {
                    $dataByStores[$storeId][$attributeName] = $category->getData($attributeName);
                }
            }
        }

        $requestPaths = $this->getCategoryUrlsByStores($category, array_filter($category->getStoreIds()));

        foreach ($dataByStores as $storeId => $data) {
            $data['id']       = $category->getId();
            $data['store_id'] = $storeId;
            $object           = new \Magento\Framework\DataObject($data);
            $requestPath      = !empty($requestPaths[$storeId]) ? $requestPaths[$storeId] : null;
            $object->setData('request_path', $requestPath);

            $this->getGenerator()->generate($object);
        }
    }

    /**
     * @param \Magento\Catalog\Model\Category $category
     */
    public function generateReportForCategoryOnSpecificStore($category)
    {
        $requestPaths = $this->getCategoryUrlsByStores($category, [$category->getStoreId()]);

        if ($requestPaths) {
            $category->setRequestPath($requestPaths[$category->getStoreId()]);
        }

        $this->getGenerator()->generate($category);
    }

    /**
     * @param \Magento\Catalog\Model\Category $category
     */
    public function regenerateReportForRemovalCategory($category)
    {
        $this->getGenerator()->regenerateByRemoval($category);
    }

    /**
     * @return \MageWorx\SeoReports\Model\Generator\CategoryReportGenerator
     */
    protected function getGenerator()
    {
        /** @var \MageWorx\SeoReports\Model\Generator\CategoryReportGenerator $generator */
        $generator = $this->generatorFactory->create('catalog_category');

        return $generator;
    }

    /**
     * @param \Magento\Catalog\Model\Category $category
     * @param int[] $stores
     * @return array
     */
    protected function getCategoryUrlsByStores($category, $stores)
    {
        $filterData = [
            UrlRewrite::ENTITY_ID        => $category->getId(),
            UrlRewrite::ENTITY_TYPE      => CategoryUrlRewriteGenerator::ENTITY_TYPE,
            UrlRewrite::IS_AUTOGENERATED => true,
        ];

        $urlRewrites = $this->urlFinder->findAllByData($filterData);

        $result = [];

        foreach ($urlRewrites as $urlRewrite) {
            $storeId = $urlRewrite->getStoreId();
            if (in_array((int)$storeId, $stores)) {
                $result[$storeId] = $urlRewrite->getRequestPath();
            }
        }

        return $result;
    }

    /**
     * @param \Magento\Catalog\Model\Category $category
     * @return array
     */
    protected function getModifiedAttributes($category)
    {
        $changedFields = [];

        foreach ($this->getFieldList() as $field) {
            if ($category->dataHasChangedFor($field)) {
                $changedFields[] = $field;
            }
        }

        return $changedFields;
    }

    /**
     * @return array
     */
    protected function getFieldList()
    {
        return $this->categoryConfig->getFieldList();
    }
}