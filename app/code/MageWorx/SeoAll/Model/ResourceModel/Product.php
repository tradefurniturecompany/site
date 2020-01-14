<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoAll\Model\ResourceModel;


class Product extends \Magento\Catalog\Model\ResourceModel\Product
{
    /**
     * Retrieve array with pairs: store_id => value
     * for text-type attribute
     *
     * @param \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute $attribute
     * @return array
     */
    public function getAttributeValues($attribute, $entityId)
    {
        $tableName = $attribute->getBackendTable();

        $connection = $this->getConnection();

        $select = $connection->select()
                             ->from($tableName, ['store_id', 'value'])
                             ->where('attribute_id = ?', $attribute->getId())
                             ->where('entity_id = ?', $entityId);

        $data = [];

        foreach ($connection->fetchAll($select) as $row) {
            $data[$row['store_id']] = $row['value'];
        }

        return $data;
    }
}
