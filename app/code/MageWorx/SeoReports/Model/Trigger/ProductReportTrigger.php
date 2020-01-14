<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoReports\Model\Trigger;

use MageWorx\SeoReports\Model\GeneratorFactory;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\CatalogUrlRewrite\Model\ProductUrlRewriteGenerator;


class ProductReportTrigger
{
    /**
     * @var \MageWorx\SeoReports\Model\Config\Product
     */
    protected $productConfig;

    /**
     * @var GeneratorFactory
     */
    protected $generatorFactory;

    /**
     * @var \MageWorx\SeoAll\Model\ResourceModel\Product
     */
    protected $productResource;

    /**
     * @var \Magento\UrlRewrite\Model\UrlFinderInterface
     */
    protected $urlFinder;

    /**
     * ProductReportTrigger constructor.
     *
     * @param \MageWorx\SeoReports\Model\Config\Product $productConfig
     * @param GeneratorFactory $generatorFactory
     * @param \MageWorx\SeoAll\Model\ResourceModel\Product $product
     * @param \Magento\UrlRewrite\Model\UrlFinderInterface $urlFinder
     */
    public function __construct(
        \MageWorx\SeoReports\Model\Config\Product $productConfig,
        \MageWorx\SeoReports\Model\GeneratorFactory $generatorFactory,
        \MageWorx\SeoAll\Model\ResourceModel\Product $product,
        \Magento\UrlRewrite\Model\UrlFinderInterface $urlFinder
    ) {
        $this->productConfig    = $productConfig;
        $this->generatorFactory = $generatorFactory;
        $this->productResource  = $product;
        $this->urlFinder        = $urlFinder;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     */
    public function generateReportForNewProduct($product)
    {
        $productStoreIds = array_filter($product->getStoreIds());
        $requestPaths    = $this->getProductUrlsByStores($product, $productStoreIds);

        foreach ($productStoreIds as $storeId) {
            $requestPath = !empty($requestPaths[$storeId]) ? $requestPaths[$storeId] : null;
            $product->setData('request_path', $requestPath);
            $product->setStoreId($storeId);
            $this->getGenerator()->generate($product);
        }

        $product->setStoreId(0);
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     */
    public function generateReportForProductOnAllStores($product)
    {
        $fieldsForCheck = $this->getModifiedAttributes($product);

        $attributesStoreValues = [];

        foreach ($fieldsForCheck as $field) {
            /** @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute */
            $attribute = $this->productResource->getAttribute($field);

            if ($attribute && !$attribute->getIsGlobal()) {
                $attributesStoreValues[$field] = $this->productResource->getAttributeValues(
                    $attribute,
                    $product->getId()
                );
            }
        }

        $dataByStores = [];

        foreach ($attributesStoreValues as $attributeName => $attributeStoreValues) {

            foreach ($product->getStoreIds() as $storeId) {

                //add new values from default scope to attributes which hasn't own values for current store view
                if (!array_key_exists($storeId, $attributeStoreValues)) {
                    $dataByStores[$storeId][$attributeName] = $product->getData($attributeName);
                }
            }
        }

        $requestPaths = $this->getProductUrlsByStores($product, array_filter($product->getStoreIds()));

        foreach ($dataByStores as $storeId => $data) {
            $data['id']       = $product->getId();
            $data['store_id'] = $storeId;
            $object           = new \Magento\Framework\DataObject($data);
            $requestPath      = !empty($requestPaths[$storeId]) ? $requestPaths[$storeId] : null;
            $object->setData('request_path', $requestPath);

            $this->getGenerator()->generate($object);
        }

        $this->getGenerator()->regenerateByRemoval($product, $product->getStoreIds());
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     */
    public function generateReportForProductOnSpecificStore($product)
    {
        $requestPaths = $this->getProductUrlsByStores($product, [$product->getStoreId()]);

        if ($requestPaths) {
            $product->setRequestPath($requestPaths[$product->getStoreId()]);
        }

        $this->getGenerator()->generate($product);
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     */
    public function regenerateReportForRemovalProduct($product)
    {
        $this->getGenerator()->regenerateByRemoval($product);
    }

    /**
     * @return \MageWorx\SeoReports\Model\Generator\ProductReportGenerator
     */
    protected function getGenerator()
    {
        /** @var \MageWorx\SeoReports\Model\Generator\ProductReportGenerator $generator */
        $generator = $this->generatorFactory->create('catalog_product');

        return $generator;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param int[] $stores
     * @return array
     */
    protected function getProductUrlsByStores($product, $stores)
    {
        $filterData = [
            UrlRewrite::ENTITY_ID        => $product->getId(),
            UrlRewrite::ENTITY_TYPE      => ProductUrlRewriteGenerator::ENTITY_TYPE,
            UrlRewrite::IS_AUTOGENERATED => true,
        ];

        $urlRewrites = $this->urlFinder->findAllByData($filterData);

        $result = [];

        foreach ($urlRewrites as $urlRewrite) {

            if (strpos($urlRewrite->getTargetPath(), '/category/') !== false) {
                continue;
            }

            $storeId = $urlRewrite->getStoreId();
            if (in_array((int)$storeId, $stores)) {
                $result[$storeId] = $urlRewrite->getRequestPath();
            }
        }

        return $result;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return array
     */
    protected function getModifiedAttributes($product)
    {
        $changedFields = [];

        foreach ($this->getFieldList() as $field) {
            if ($product->dataHasChangedFor($field)) {
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
        return $this->productConfig->getFieldList();
    }
}