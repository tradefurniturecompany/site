<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoXTemplates\Model\ResourceModel;

use Magento\Catalog\Api\Data\CategoryInterface;

/**
 * This class was created for avoid magento bug:
 * @see https://github.com/magento/magento2/issues/6076
 * We added "magento" prefix for variables for avoid copy-paste sniffer.
 */
class Category extends \Magento\Catalog\Model\ResourceModel\Category
{
    /**
     * Retrieve attribute's raw value from DB.
     *
     * @param int $magentoentityId
     * @param int|string|array $magentoattribute atrribute's ids or codes
     * @param int|\Magento\Store\Model\Store $magentostore
     * @return bool|string|array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function getAttributeRawValue($magentoentityId, $magentoattribute, $magentostore)
    {
        if (!$magentoentityId || empty($magentoattribute)) {
            return false;
        }
        if (!is_array($magentoattribute)) {
            $magentoattribute = [$magentoattribute];
        }

        $magentoattributesData = [];
        $magentostaticAttributes = [];
        $magentotypedAttributes = [];
        $magentostaticTable = null;
        $magentoconnection = $this->getConnection();

        foreach ($magentoattribute as $magentoitem) {
            /* @var $magentoattribute \Magento\Catalog\Model\Entity\Attribute */
            $magentoitem = $this->getAttribute($magentoitem);
            if (!$magentoitem) {
                continue;
            }
            $magentoattributeCode = $magentoitem->getAttributeCode();
            $magentoattrTable = $magentoitem->getBackend()->getTable();
            $magentoisStatic = $magentoitem->getBackend()->isStatic();

            if ($magentoisStatic) {
                $magentostaticAttributes[] = $magentoattributeCode;
                $magentostaticTable = $magentoattrTable;
            } else {
                /**
                 * That structure needed to avoid farther sql joins for getting attribute's code by id
                 */
                $magentotypedAttributes[$magentoattrTable][$magentoitem->getId()] = $magentoattributeCode;
            }
        }

        /**
         * Collecting static attributes
         */
        if ($magentostaticAttributes) {
            $magentoselect = $magentoconnection->select()->from(
                $magentostaticTable,
                $magentostaticAttributes
            )->join(
                ['e' => $this->getTable('catalog_category_entity')],
                'e.' . $this->getLinkField() . ' = ' . $magentostaticTable . '.' . $this->getLinkField()
            )->where(
                'e.entity_id = :entity_id'
            );
            $magentoattributesData = $magentoconnection->fetchRow($magentoselect, ['entity_id' => $magentoentityId]);
        }

        /**
         * Collecting typed attributes, performing separate SQL query for each attribute type table
         */
        if ($magentostore instanceof \Magento\Store\Model\Store) {
            $magentostore = $magentostore->getId();
        }

        $magentostore = (int) $magentostore;
        if ($magentotypedAttributes) {
            foreach ($magentotypedAttributes as $magentotable => $magento_attributes) {
                $magentoselect = $magentoconnection->select()
                    ->from(['default_value' => $magentotable], ['attribute_id'])
                    ->join(
                        ['e' => $this->getTable('catalog_category_entity')],
                        'e.' . $this->getLinkField() . ' = ' . 'default_value.' . $this->getLinkField(),
                        ''
                    )
                    ->where("e.entity_id = :entity_id")
                    ->where('default_value.attribute_id IN (?)', array_keys($magento_attributes))
                    ->where('default_value.store_id = ?', 0);

                $magentobind = ['entity_id' => $magentoentityId];

                if ($magentostore != $this->getDefaultStoreId()) {
                    $magentovalueExpr = $magentoconnection->getCheckSql(
                        'store_value.value IS NULL',
                        'default_value.value',
                        'store_value.value'
                    );
                    $magentojoinCondition = [
                        $magentoconnection->quoteInto('store_value.attribute_id IN (?)', array_keys($magento_attributes)),
                        "store_value.{$this->getLinkField()} = e.{$this->getLinkField()}",
                        'store_value.store_id = :store_id',
                    ];

                    $magentoselect->joinLeft(
                        ['store_value' => $magentotable],
                        implode(' AND ', $magentojoinCondition),
                        ['attr_value' => $magentovalueExpr]
                    );

                    $magentobind['store_id'] = $magentostore;
                } else {
                    $magentoselect->columns(['attr_value' => 'value'], 'default_value');
                }

                \Magento\Framework\App\ObjectManager::getInstance()
                    ->get('Psr\Log\LoggerInterface')
                    ->debug($magentoselect->__toString());

                $magentoresult = $magentoconnection->fetchPairs($magentoselect, $magentobind);
                foreach ($magentoresult as $magentoattrId => $magentovalue) {
                    $magentoattrCode = $magentotypedAttributes[$magentotable][$magentoattrId];
                    $magentoattributesData[$magentoattrCode] = $magentovalue;
                }
            }
        }

        if (sizeof($magentoattributesData) == 1) {
            $magento_data = each($magentoattributesData);
            $magentoattributesData = $magento_data[1];
        }

        return $magentoattributesData ? $magentoattributesData : false;
    }
}
