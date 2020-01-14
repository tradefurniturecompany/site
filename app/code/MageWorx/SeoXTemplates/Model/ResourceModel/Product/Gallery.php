<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Model\ResourceModel\Product;

use Magento\Store\Model\Store;

class Gallery extends \Magento\Catalog\Model\ResourceModel\Product\Gallery
{
    /**
     * @param int $storeId
     * @param int $attributeId
     * @return \Magento\Framework\DB\Select
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createImageBatchBaseSelect($storeId, $attributeId)
    {
        $linkField      = $this->metadata->getLinkField();
        $mainTableAlias = $this->getMainTableAlias();

        $select = $this->getConnection()->select()->from(
            [$mainTableAlias => $this->getMainTable()],
            [
                'value_id',
                'file' => 'value',
                'media_type'
            ]
        )->joinInner(
            ['entity' => $this->getTable(self::GALLERY_VALUE_TO_ENTITY_TABLE)],
            $mainTableAlias . '.value_id = entity.value_id',
            [$linkField]
        )->joinLeft(
            ['value' => $this->getTable(self::GALLERY_VALUE_TABLE)],
            implode(
                ' AND ',
                [
                    $mainTableAlias . '.value_id = value.value_id',
                    $this->getConnection()->quoteInto('value.store_id = ?', (int)$storeId),
                ]
            ),
            ['label', 'position', 'disabled']
        )->joinLeft(
            ['default_value' => $this->getTable(self::GALLERY_VALUE_TABLE)],
            implode(
                ' AND ',
                [
                    $mainTableAlias . '.value_id = default_value.value_id',
                    $this->getConnection()->quoteInto('default_value.store_id = ?', Store::DEFAULT_STORE_ID),
                ]
            ),
            ['label_default' => 'label', 'position_default' => 'position', 'disabled_default' => 'disabled']
        )->where(
            $mainTableAlias . '.attribute_id = ?',
            $attributeId
        )->order(
            'entity.' . $linkField,
            'entity.value_id'
        );

        return $select;
    }

    /**
     * Add filter to product collection - products which has image(s) without image label on store level
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function addEmptyLabelFilter($collection)
    {
        /** @var $attribute \Magento\Catalog\Model\ResourceModel\Eav\Attribute */
        $attribute = $collection->getAttribute('media_gallery');

        $linkField = $this->metadata->getLinkField();

        $mainTableAlias = 'gallery';

        $collection->getSelect()
                   ->joinInner(
                       ['value_to_entity' => $this->getTable(self::GALLERY_VALUE_TO_ENTITY_TABLE)],
                       'value_to_entity.' . $linkField . ' = e.' . $linkField,
                       []
                   )
                   ->joinLeft(
                       [$mainTableAlias => $this->getMainTable()],
                       'gallery.value_id = value_to_entity.value_id',
                       []
                   )
                   ->joinLeft(
                       ['value' => $this->getTable(self::GALLERY_VALUE_TABLE)],
                       implode(
                           ' AND ',
                           [
                               $mainTableAlias . '.value_id = value.value_id',
                               $this->getConnection()->quoteInto(
                                   'value.store_id = ?',
                                   (int)$collection->getStoreId()
                               ),
                           ]
                       ),
                       ['label', 'position', 'disabled']
                   )
                   ->joinLeft(
                       ['default_value' => $this->getTable(self::GALLERY_VALUE_TABLE)],
                       implode(
                           ' AND ',
                           [
                               $mainTableAlias . '.value_id = default_value.value_id',
                               $this->getConnection()->quoteInto(
                                   'default_value.store_id = ?',
                                   Store::DEFAULT_STORE_ID
                               ),
                           ]
                       ),
                       ['label_default' => 'label', 'position_default' => 'position', 'disabled_default' => 'disabled']
                   )
                   ->where(
                       $mainTableAlias . '.attribute_id = ?',
                       $attribute->getAttributeId()
                   )
                   ->where("value.label IS NULL OR value.label = ''")
                   ->group('e.' . $linkField);
    }

}