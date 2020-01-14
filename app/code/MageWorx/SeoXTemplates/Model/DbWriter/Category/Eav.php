<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Model\DbWriter\Category;

use Magento\Catalog\Model\ResourceModel\Category;
use Magento\Framework\App\ResourceConnection;
use MageWorx\SeoXTemplates\Model\DataProviderCategoryFactory;
use Magento\Catalog\Api\Data\CategoryInterface;
use MageWorx\SeoAll\Helper\LinkFieldResolver;

class Eav extends \MageWorx\SeoXTemplates\Model\DbWriter\Category
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
     * @param DataProviderCategoryFactory $dataProviderCategoryFactory
     * @param Category $categoryResource
     * @param LinkFieldResolver $linkFieldResolver
     */
    public function __construct(
        ResourceConnection $resource,
        DataProviderCategoryFactory $dataProviderCategoryFactory,
        Category $categoryResource,
        LinkFieldResolver $linkFieldResolver
    ) {
        parent::__construct($resource, $dataProviderCategoryFactory);
        $this->categoryResource = $categoryResource;
        $this->linkFieldResolver = $linkFieldResolver;
    }

    public function write($collection, $template, $customStoreId = null)
    {
        if (!$collection) {
            return false;
        }

        $this->_collection = $collection;

        $dataProvider = $this->dataProviderCategoryFactory->create($template->getTypeId());
        $data         = $dataProvider->getData($collection, $template, $customStoreId);

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
            $tableName = $this->_resource->getTableName('catalog_category_entity_' . $backendType);

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
     * Insert process
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
     * Update process
     *
     * @param string $table
     * @param array $multipleData
     */
    protected function doUpdate($table, $multipleData)
    {
        $linkField = $this->linkFieldResolver->getLinkField(CategoryInterface::class, 'entity_id');
        if (!empty($multipleData)) {
            foreach ($multipleData as $data) {
                if (empty($data['value'])) {
                    continue;
                }

                $where = [
                    'attribute_id = ?'  => (int)$data['attribute_id'],
                     $linkField.'  = ?' => (int)$data[$linkField],
                    'store_id =?'       => (int)$data['store_id']
                ];
                $bind = ['value' => $data['value']];
                $this->_connection->update($table, $bind, $where);
            }
        }
    }
}
