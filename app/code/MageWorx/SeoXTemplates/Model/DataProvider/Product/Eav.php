<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Model\DataProvider\Product;

use Magento\Framework\App\ResourceConnection;
use Magento\Catalog\Model\ResourceModel\Product;
use MageWorx\SeoXTemplates\Model\ConverterProductFactory;
use MageWorx\SeoAll\Helper\LinkFieldResolver;
use MageWorx\SeoXTemplates\Helper\Store as HelperStore;
use Magento\Catalog\Api\Data\ProductInterface;

class Eav extends \MageWorx\SeoXTemplates\Model\DataProvider\Product
{
    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $_connection;

    /**
     *
     * @var int
     */
    protected $_defaultStore;

    /**
     * Store ID for obtaining and preparing data
     *
     * @var int
     */
    protected $_storeId;

    /**
     * @var HelperStore
     */
    protected $helperStore;

    /**
     *
     * @var array
     */
    protected $_attributeCodes = [];

    /**
     *
     * @var \Magento\Framework\Data\Collection
     */
    protected $_collection;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product
     */
    protected $productResource;

    /**
     * @var \MageWorx\SeoAll\Helper\LinkFieldResolver
     */
    protected $linkFieldResolver;

    /**
     * Eav constructor.
     * @param ResourceConnection $resource
     * @param ConverterProductFactory $converterProductFactory
     * @param LinkFieldResolver $linkFieldResolver
     * @param Product $productResource
     * @param HelperStore $helperStore
     */
    public function __construct(
        ResourceConnection $resource,
        ConverterProductFactory $converterProductFactory,
        LinkFieldResolver $linkFieldResolver,
        Product $productResource,
        HelperStore $helperStore
    ) {
        parent::__construct($resource, $converterProductFactory);
        $this->linkFieldResolver = $linkFieldResolver;
        $this->productResource   = $productResource;
        $this->helperStore       = $helperStore;
    }

    /**
     * Retrieve data
     *
     * @param \Magento\Framework\Data\Collection $collection
     * @param \MageWorx\SeoXTemplates\Model\Template\Product $template
     * @param int|null $customStoreId
     * @return array
     */
    public function getData($collection, $template, $customStoreId = null)
    {
        if (!$collection) {
            return false;
        }

        $this->_collection = $collection;
        $this->_storeId    = $this->getStoreId($template, $customStoreId);

        $this->_attributeCodes = $template->getAttributeCodesByType();

        $attributes  = [];
        $connection  = $this->_getConnection();

        $select        = $connection->select()
            ->from($this->_resource->getTableName('eav_entity_type'))
            ->where("entity_type_code = 'catalog_product'");
        $productTypeId = $connection->fetchOne($select);

        foreach ($this->_attributeCodes as $_attrName) {
            $select                 = $connection->select()
                ->from($this->_resource->getTableName('eav_attribute'))
                ->where("entity_type_id = $productTypeId AND (attribute_code = '" . $_attrName . "')");

            if ($res = $connection->fetchRow($select)) {
                $attributes[$_attrName] = $res;
            }
        }

        $productIds       =  array_keys($this->getCollectionIds());
        $productIdsString = implode(',', $productIds);

        $data = [];

        $linkField = $this->getLinkField();

        foreach ($attributes as $attribute) {
            $idsByAttribute = [
                'insert' => array_fill_keys($productIds, []),
                'update' => []
            ];

            $tableName = $this->_resource->getTableName('catalog_product_entity') . '_' . $attribute['backend_type'];

            if ($template->getIsSingleStoreMode()) {
                $condition = "AND store_id = {$template->getStoreId()}";
            } else {
                $condition = "AND store_id = {$this->_storeId}";
            }

            $select = $connection->select([$linkField])
                ->from($tableName)
                ->where("attribute_id = '$attribute[attribute_id]'
                    AND $linkField IN ({$productIdsString}) " . $condition);

            $existRecords = $connection->fetchAll($select);
            foreach ($existRecords as $record) {
                if ($template->isScopeForAll() || $record['value'] == null) {
                    $idsByAttribute['update'][$record[$linkField]] = ['old_value' => $record['value']];
                }
                unset($idsByAttribute['insert'][$record[$linkField]]);
            }

            $attributeHash = $attribute['attribute_id'] . '#' .  $attribute['attribute_code'] . '#' . $attribute['backend_type'];
            $data[$attributeHash] = $idsByAttribute;
        }

        $this->fillData($template, $data);

        return $data;
    }

    /**
     * @param \MageWorx\SeoXTemplates\Model\Template\Product $template
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     * @return mixed|void
     */
    public function addFiltersToEntityCollection($template, $collection)
    {
        if ($template::SCOPE_EMPTY == $template->getScope()) {
            $attributes = $template->getAttributeCodesByType();

            foreach ($attributes as $attributeCode) {
                $collection->addAttributeToSelect($attributeCode);
                $collection->addAttributeToFilter($attributeCode, ['null' => true], 'left');
            }
        }

        return parent::addFiltersToEntityCollection($template, $collection);
    }

    /**
     * @param \MageWorx\SeoXTemplates\Model\AbstractTemplate $template
     * @param int|null $customStoreId
     * @return int|null
     */
    protected function getStoreId($template, $customStoreId = null)
    {
        if ($customStoreId) {
            return $customStoreId;
        }

        if ($template->getIsSingleStoreMode()) {
            return $this->helperStore->getCurrentStoreId();
        }

        return $template->getStoreId();
    }

    /**
     * Add data for each entityId
     *
     * @param array $data
     */
    protected function fillData($template, &$data)
    {
        $storeIdForApply = $template->getIsSingleStoreMode() ? $template->getStoreId() : $this->_storeId;

        $connect = $this->getCollectionIds();
        foreach ($data as $attributeHash => $attributeData) {
            list($attributeId, $attributeCode) = explode('#', $attributeHash);

            $converter = $this->converterProductFactory->create($attributeCode);
            $linkField = $this->getLinkField();
            foreach ($attributeData as $insertTypeName => $insertData) {
                foreach ($insertData as $entityId => $emptyValue) {
//                    $microtime = microtime(1);
                    $attributeValue = '';
                    $product = $this->_collection->getItemById($connect[$entityId]);
                    if ($product) {
                        $attributeValue = $converter->convert($product->setStoreId($this->_storeId), $template->getCode());
                    }

//                    echo "<br><font color = green>" . number_format((microtime(1) - $microtime), 5) . " sec need for " . get_class($this) . "</font>";

                    if ($attributeValue) {
                        $data[$attributeHash][$insertTypeName][$entityId] = array_merge($data[$attributeHash][$insertTypeName][$entityId], [
                            'attribute_id' => $attributeId,
                            $linkField     => $entityId,
                            'store_id'     => $storeIdForApply,
                            'value'        => $attributeValue,
                        ]);
                    } else {
                        unset($data[$attributeCode][$insertTypeName][$entityId]);
                    }
                }
            }
        }
    }

    public function getAttributeCodes()
    {
        return $this->_attributeCodes;
    }

    /**
     * return array row_id => entity_id or entity_id => entity_id
     */
    public function getCollectionIds()
    {
        $data = [];
        $linkField = $this->getLinkField();
        foreach ($this->_collection as $item) {
            $data[$item->getData($linkField)] = $item->getData('entity_id');
        }
        return $data;
    }

    /**
     * @return string
     */
    protected function getLinkField()
    {
        return $this->linkFieldResolver->getLinkField(ProductInterface::class, 'entity_id');
    }
}
