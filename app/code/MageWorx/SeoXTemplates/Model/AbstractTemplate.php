<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Message\ManagerInterface;

/**
 * @method int getTypeId()
 * @method Template setTypeId(\int $title)
 * @method string getName()
 * @method Template setName(\string $name)
 * @method int getStoreId()
 * @method Template setStoreId(\int $storeId)
 * @method string getCode()
 * @method Template setCode(\string $code)
 * @method int getAssignType()
 * @method Template setAssignType(\int $assignType)
 * @method int getPriority()
 * @method Template setPriority(\int $priority)
 * @method string getDateModified()
 * @method Template setDateModified(\string $dateModified)
 * @method string getDateApplyStart()
 * @method Template setDateApplyStart(\string $dateApplyStart)
 * @method string getDateApplyFinish()
 * @method Template setDateApplyFinish(\string $dateApplyFinish)
 * @method int geScope()
 * @method Template setScope(\int $scope)
 * @method bool getUseCron()
 * @method Template setIsUseCrone(\bool $isUseCron)
 * @method bool getIsUseCrone()
 * @method int getIsSingleStoreMode()
 * @method Template setIsSingleStoreMode(\int $isSingleStoreMode)
 */
abstract class AbstractTemplate extends AbstractModel
{
    /**
     * Assign type value for all items
     *
     * @var int
     */
    const ASSIGN_ALL_ITEMS        = '1';

    /**
     * Assign type value for individual items
     *
     * @var int
     */
    const ASSIGN_INDIVIDUAL_ITEMS = '2';

    /**
     * Scope value for entities with empty required value
     *
     * @var int
     */
    const SCOPE_EMPTY = 1;

    /**
     * Scope value for entities with any value
     *
     * @var int
     */
    const SCOPE_ALL   = 2;

    /**
     *
     * @var int
     */
    const CRON_ENABLED  = 1;

    /**
     *
     * @var int
     */
    const CRON_DISABLED = 2;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * Calc priority for template
     *
     * @return int
     */
    abstract protected function calcPriority();

    /**
     *
     * @param Context $context
     * @param Registry $registry
     * @param AbstractResource $resource
     * @param AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ManagerInterface $messageManager,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
    
        $this->messageManager = $messageManager;

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     *
     * @param int|null $value
     * @return bool
     */
    public function isScopeForAll($value = null)
    {
        $value = $value ? $value : $this->getScope();
        return $value == self::SCOPE_ALL;
    }

    /**
     *
     * @param int|null $value
     * @return bool
     */
    public function isScopeForEmpty($value = null)
    {
        $value = $value ? $value : $this->getScope();
        return $value == self::SCOPE_EMPTY;
    }

    /**
     *
     * @param int|null $assignType
     * @return bool
     */
    public function isAssignForAllItems($assignType = null)
    {
        $assignType = $assignType ? $assignType : $this->getAssignType();
        return self::ASSIGN_ALL_ITEMS == $assignType;
    }

    /**
     *
     * @param int|null $assignType
     * @return bool
     */
    public function isAssignForIndividualItems($assignType = null)
    {
        $assignType = $assignType ? $assignType : $this->getAssignType();
        return self::ASSIGN_INDIVIDUAL_ITEMS == $assignType;
    }

    /**
     * Process template before saving
     *
     * @param \Magento\Catalog\Model\Category $object
     * @return MageWorx_SeoXTemplates_Model_Mysql4_Template
     */
    public function beforeSave()
    {
        $this->setPriority($this->calcPriority());
        return parent::beforeSave();
    }

    /**
     * Check if the template that is assigned to all items already exists
     *
     * @param int $nestedStoreId
     * @return boolean
     */
    protected function issetUniqStoreTemplateForAllItems($nestedStoreId)
    {
        if ($this->getStoreId() == '0' && !$this->getIsSingleStoreMode()) {
            $templateCollection = $this->getCollection()
                ->addStoreModeFilter($this->getIsSingleStoreMode())
                ->addTypeFilter($this->getTypeId())
                ->addAssignTypeFilter(self::ASSIGN_ALL_ITEMS)
                ->addSpecificStoreFilter($nestedStoreId);

            if ($templateCollection->count()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Retrieve duplicate template that is assigned to all items
     *
     * @return $this|false
     */
    public function getDuplicateTemplateAssignedForAll()
    {
        $templateCollection = $this->getCollection()
            ->addStoreModeFilter($this->getIsSingleStoreMode())
            ->addSpecificStoreFilter($this->getStoreId())
            ->addTypeFilter($this->getTypeId())
            ->addAssignTypeFilter(self::ASSIGN_ALL_ITEMS);
        if ($this->getTemplateId()) {
            $templateCollection->excludeTemplateFilter($this->getTemplateId());
        }

        if ($templateCollection->count()) {
            return $templateCollection->getFirstItem();
        }
        return false;
    }
}
