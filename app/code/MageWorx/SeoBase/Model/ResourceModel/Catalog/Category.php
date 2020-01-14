<?php
/**
 * Copyright © 2015 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoBase\Model\ResourceModel\Catalog;

use Magento\Catalog\Api\Data\CategoryInterface;

/**
 * SEO Base resource category collection model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Category extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Collection Zend Db select
     *
     * @var \Zend_Db_Select
     */
    protected $select;

    /**
     * Attribute cache
     *
     * @var array
     */
    protected $attributesCache = [];

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category
     */
    protected $categoryResource;

    /**
     * @var \MageWorx\SeoAll\Helper\LinkFieldResolver
     */
    protected $linkFieldResolver;

    /**
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Catalog\Model\ResourceModel\Category $categoryResource
     * @param string|null $resourcePrefix
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Catalog\Model\ResourceModel\Category $categoryResource,
        \MageWorx\SeoAll\Helper\LinkFieldResolver $linkFieldResolver,
        $resourcePrefix = null
    ) {
        $this->linkFieldResolver = $linkFieldResolver;
        $this->categoryResource = $categoryResource;
        parent::__construct($context, $resourcePrefix);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('catalog_category_entity', 'entity_id');
    }

    /**
     * Add attribute to filter
     *
     * @param int $storeId
     * @param string $attributeCode
     * @param mixed $value
     * @param string $type
     * @return \Zend_Db_Select|bool
     */
    protected function addFilter($storeId, $attributeCode, $value, $type = '=')
    {
        if (!$this->select instanceof \Zend_Db_Select) {
            return false;
        }

        $linkField = $this->linkFieldResolver->getLinkField(CategoryInterface::class, 'entity_id');

        if (!isset($this->attributesCache[$attributeCode])) {
            $attribute = $this->categoryResource->getAttribute($attributeCode);

            $this->attributesCache[$attributeCode] = [
                'entity_type_id' => $attribute->getEntityTypeId(),
                'attribute_id' => $attribute->getId(),
                'table' => $attribute->getBackend()->getTable(),
                'is_global' => $attribute->getIsGlobal(),
                'backend_type' => $attribute->getBackendType(),
            ];
        }
        $attribute = $this->attributesCache[$attributeCode];

        switch ($type) {
            case '=':
                $conditionRule = '=?';
                break;
            case 'in':
                $conditionRule = ' IN(?)';
                break;
            default:
                return false;
        }

        if ($attribute['backend_type'] == 'static') {
            $this->select->where('e.' . $attributeCode . $conditionRule, $value);
        } else {
            $this->select->join(
                ['t1_' . $attributeCode => $attribute['table']],
                'e.' . $linkField . ' = t1_' . $attributeCode . '.' . $linkField .
                ' AND t1_' . $attributeCode . '.store_id = 0',
                []
            )->where(
                't1_' . $attributeCode . '.attribute_id=?',
                $attribute['attribute_id']
            );

            if ($attribute['is_global']) {
                $this->select->where('t1_' . $attributeCode . '.value' . $conditionRule, $value);
            } else {
                $ifCase = $this->select->getAdapter()->getCheckSql(
                    't2_' . $attributeCode . '.value_id > 0',
                    't2_' . $attributeCode . '.value',
                    't1_' . $attributeCode . '.value'
                );
                $this->select->joinLeft(
                    ['t2_' . $attributeCode => $attribute['table']],
                    $this->getConnection()->quoteInto(
                        't1_' .
                        $attributeCode .
                        '.' . $linkField . ' = t2_' .
                        $attributeCode .
                        '.' . $linkField . ' AND t1_' .
                        $attributeCode .
                        '.attribute_id = t2_' .
                        $attributeCode .
                        '.attribute_id AND t2_' .
                        $attributeCode .
                        '.store_id=?',
                        $storeId
                    ),
                    []
                )->where(
                    '(' . $ifCase . ')' . $conditionRule,
                    $value
                );
            }
        }

        return $this->select;
    }

    /**
     * Join attribute by code
     *
     * @param int $storeId
     * @param string $attributeCode
     * @param string $addToResult
     * @return void
     */
    protected function joinAttribute($storeId, $attributeCode, $addToResult = false)
    {
        $adapter = $this->getReadConnection();
        $attribute = $this->getAttribute($attributeCode);
        $linkField = $this->linkFieldResolver->getLinkField(CategoryInterface::class, 'entity_id');
        $this->select->joinLeft(
            ['t1_' . $attributeCode => $attribute['table']],
            'e.' . $linkField . ' = t1_' . $attributeCode . '.' . $linkField . ' AND ' . $adapter->quoteInto(
                ' t1_' . $attributeCode . '.store_id = ?',
                \Magento\Store\Model\Store::DEFAULT_STORE_ID
            ) . $adapter->quoteInto(
                ' AND t1_' . $attributeCode . '.attribute_id = ?',
                $attribute['attribute_id']
            ),
            $addToResult ? [$attributeCode => 't1_' . $attributeCode . '.value'] : []
        );

        if (!$attribute['is_global']) {
            $this->select->joinLeft(
                ['t2_' . $attributeCode => $attribute['table']],
                $this->_getWriteAdapter()->quoteInto(
                    't1_' .
                    $attributeCode .
                    '.' . $linkField . ' = t2_' .
                    $attributeCode .
                    '.' . $linkField . ' AND t1_' .
                    $attributeCode .
                    '.attribute_id = t2_' .
                    $attributeCode .
                    '.attribute_id AND t2_' .
                    $attributeCode .
                    '.store_id = ?',
                    $storeId
                ),
                []
            );
        }
    }

    /**
     * Get attribute data by attribute code
     *
     * @param string $attributeCode
     * @return array
     */
    protected function getAttribute($attributeCode)
    {
        if (!isset($this->attributesCache[$attributeCode])) {
            $attribute = $this->categoryResource->getAttribute($attributeCode);

            $this->attributesCache[$attributeCode] = [
                'entity_type_id' => $attribute->getEntityTypeId(),
                'attribute_id' => $attribute->getId(),
                'table' => $attribute->getBackend()->getTable(),
                'is_global' => $attribute->getIsGlobal() ==
                \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                'backend_type' => $attribute->getBackendType(),
            ];
        }
        return $this->attributesCache[$attributeCode];
    }
}
