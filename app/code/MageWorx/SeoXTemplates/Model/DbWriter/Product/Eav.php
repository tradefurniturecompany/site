<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Model\DbWriter\Product;

use Magento\Framework\App\ResourceConnection;
use MageWorx\SeoXTemplates\Model\DataProviderProductFactory;
use Magento\Catalog\Model\ResourceModel\Product;
use Magento\Catalog\Api\Data\ProductInterface;
use MageWorx\SeoAll\Helper\LinkFieldResolver;

class Eav extends \MageWorx\SeoXTemplates\Model\DbWriter\Product
{

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product
     */
    protected $productResource;

    /**
     * @var \MageWorx\SeoXTemplates\Model\DataProviderInterface
     */
    protected $dataProvider;

    /**
     * @var \MageWorx\SeoAll\Helper\LinkFieldResolver
     */
    protected $linkFieldResolver;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected $_collection;

    /**
     * Eav constructor.
     * @param ResourceConnection $resource
     * @param DataProviderProductFactory $dataProviderProductFactory
     * @param Product $productResource
     * @param LinkFieldResolver $linkFieldResolver
     */
    public function __construct(
        ResourceConnection $resource,
        DataProviderProductFactory $dataProviderProductFactory,
        Product $productResource,
        LinkFieldResolver $linkFieldResolver
    ) {
        parent::__construct($resource, $dataProviderProductFactory);
        $this->productResource = $productResource;
        $this->linkFieldResolver = $linkFieldResolver;
    }

    /**
     * Write to database converted string from template code
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     * @param \MageWorx\SeoXTemplates\Model\AbstractTemplate $template
     * @param int $customStoreId
     * @return array|false
     */

    public function write($collection, $template, $customStoreId = null)
    {
        if (!$collection) {
            return false;
        }

        $this->_collection = $collection;

        $this->dataProvider = $this->dataProviderProductFactory->create($template->getTypeId());
        $data         = $this->dataProvider->getData($collection, $template, $customStoreId);

        foreach ($data as $attributeHash => $attributeData) {
            $this->attributeDataWrite($attributeHash, $attributeData);
        }

        return true;
    }

    /**
     * Write dispatch
     *
     * @param string $hash
     * @param array $attributeData
     */
    protected function attributeDataWrite($hash, $attributeData)
    {
        foreach ($attributeData as $insertType => $multipleData) {
            list($attributeId, $attributeCode, $backendType) = explode('#', $hash);
            $tableName = $this->_resource->getTableName('catalog_product_entity_' . $backendType);

            switch ($insertType) :
                case 'insert':
                    $this->doInsert($tableName, $multipleData);
                    break;
                case 'update':
                    $this->doUpdate($tableName, $multipleData);
                    break;
            endswitch;
        }
    }

    /**
     * Insert proccess
     *
     * @param string $table
     * @param array $multipleData
     */
    protected function doInsert($table, $multipleData)
    {
        $multipleData = array_filter($multipleData);

        if (!empty($multipleData)) {
                $this->_connection->insertMultiple(
                    $table,
                    $multipleData
                );
        }
    }

    /**
     * Update proccess
     *
     * @param string $table
     * @param array $multipleData
     */
    protected function doUpdate($table, $multipleData)
    {
        $linkField = $this->linkFieldResolver->getLinkField(ProductInterface::class, 'entity_id');
        if (!empty($multipleData)) {
            foreach ($multipleData as $data) {
                $where = [
                    'attribute_id = ?' => (int)$data['attribute_id'],
                    $linkField.' = ?'  => (int)$data[$linkField],
                    'store_id =?'      => (int)$data['store_id']
                ];
                $bind = ['value' => $data['value']];
                $this->_connection->update($table, $bind, $where);
            }
        }
    }
}
