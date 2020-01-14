<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Model\ResourceModel\Template\Category;

/**
 * Collection
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'template_id';

    /**
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\DB\Adapter\AdapterInterface|null $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb|null $resource
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('MageWorx\SeoXTemplates\Model\Template\Category', 'MageWorx\SeoXTemplates\Model\ResourceModel\Template\Category');
    }

    /**
     * Add cron filter
     *
     * @return $this
     */
    public function addCronFilter()
    {
        $this->getSelect()->where(
            'main_table.is_use_cron = ?',
            \MageWorx\SeoXTemplates\Model\AbstractTemplate::CRON_ENABLED
        );
        return $this;
    }

    /**
     * Add store filter
     *
     * @param int $id
     * @return $this
     */
    public function addStoreFilter($id)
    {
        $this->getSelect()->where('main_table.store_id = ' . $id . ' or main_table.store_id = 0');
        return $this;
    }

    /**
     * Add type filter
     *
     * @param int|array $ids
     * @return $this
     */
    public function addTypeFilter($ids)
    {
        if (!is_array($ids)) {
            $ids = [$ids];
        }
        if (!empty($ids)) {
            $this->getSelect()->where('main_table.type_id IN (?)', $ids);
        }
        return $this;
    }

    /**
     * Add Store Mode filter
     *
     * @param int $isSingleStoreMode
     * @return $this
     */
    public function addStoreModeFilter($isSingleStoreMode)
    {
        $this->getSelect()->where('main_table.is_single_store_mode = ?', $isSingleStoreMode);
        return $this;
    }

    /**
     * Add assign type filter
     *
     * @param int|array $ids
     * @return $this
     */
    public function addAssignTypeFilter($ids)
    {
        if (!is_array($ids)) {
            $ids = [$ids];
        }
        if (!empty($ids)) {
            $this->getSelect()->where('main_table.assign_type IN (?)', $ids);
        }
        return $this;
    }

    /**
     * Add template filter
     *
     * @param int|array $ids
     * @return $this
     */
    public function excludeTemplateFilter($ids)
    {
        if (!is_array($ids)) {
            $ids = [$ids];
        }
        if (!empty($ids)) {
            $this->getSelect()->where('main_table.template_id NOT IN (?)', $ids);
        }
        return $this;
    }

    /**
     * Add store filter
     *
     * @param int $id
     * @return $this
     */
    public function addSpecificStoreFilter($id)
    {
        $this->getSelect()->where('main_table.store_id = ?', $id);
        return $this;
    }

    /**
     * Add reset filter
     *
     * @return $this
     */
    public function addResetFilter()
    {
        $this->getSelect()->reset('where');
        return $this;
    }

    /**
     *
     * @param int|array $ids
     * @return $this
     */
    public function loadByIds($ids)
    {
        if (!is_array($ids)) {
            $ids = [$ids];
        }
        if (!empty($ids)) {
            $this->getSelect()->where('main_table.template_id IN (?)', $ids);
        }
        return $this;
    }
}
