<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoExtended\Model\ResourceModel\CategoryFilter;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use MageWorx\SeoExtended\Model\CategoryFilter;
use MageWorx\SeoExtended\Model\ResourceModel\CategoryFilter as CategoryFilterResourceModel;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'id';
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'mageworx_seoextended_categoryFilter_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'mageworx_seoextended_categoryFilter_collection';

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var array
     */
    protected $_joinedFields = [];

    /**
     * constructor
     *
     * @param EntityFactoryInterface $entityFactory
     * @param LoggerInterface $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param StoreManagerInterface $storeManager
     * @param null $connection
     * @param AbstractDb $resource
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        StoreManagerInterface $storeManager,
        $connection = null,
        AbstractDb $resource = null
    ) {
        $this->storeManager = $storeManager;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(CategoryFilter::class, CategoryFilterResourceModel::class);
        $this->_map['fields']['id'] = 'main_table.id';
        $this->_map['fields']['category_id'] = 'main_table.category_id';
        $this->_map['fields']['attribute_id'] = 'main_table.attribute_id';
        $this->_map['fields']['store_id'] = 'main_table.store_id';
    }

    /**
     * Add filtering by ids
     *
     * @param array $ids
     * @return $this
     */
    public function addIdsToFilter(array $ids)
    {
        $this->addFieldToFilter('main_table.id', ['in' => $ids]);

        return $this;
    }


    /**
     * @param int/array $attributeIds
     * @param int $categoryId
     * @param int $storeId
     * @return $this
     */
    public function getFilteredCollection($attributeIds, $categoryId, $storeId, $attributeOptionIds = [])
    {
        if (!is_array($attributeIds) && $attributeIds) {
            $attributeIds = [$attributeIds];
        }

        $this->addFieldToFilter('attribute_id', ['in' => $attributeIds]);
        $this->addFieldToFilter('category_id', $categoryId);
        $this->addFieldToFilter('store_id', $storeId);

        if ($attributeOptionIds) {
            $this->addFieldToFilter('attribute_option_id', ['in' => $attributeOptionIds]);
        }

        $this->getSelect()->order('attribute_option_id desc');

        return $this;
    }
}
