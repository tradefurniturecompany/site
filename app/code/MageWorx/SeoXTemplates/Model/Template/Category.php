<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Model\Template;

use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Store\Model\Store;
use MageWorx\SeoXTemplates\Model\Template\Category\Source\AttributeCode as AttributeCodeOptions;
use MageWorx\SeoXTemplates\Model\Template\Source\AssignType as AssignTypeOptions;
use MageWorx\SeoXTemplates\Model\Template\Source\Scope as ScopeOptions;
use MageWorx\SeoXTemplates\Model\Template\Source\IsUseCron as IsUseCronOptions;
use MageWorx\SeoXTemplates\Model\Template\Category\Source\Type as TypeOptions;
use MageWorx\SeoXTemplates\Helper\Store as HelperStore;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Framework\Message\ManagerInterface as MagentoManagerInterface;

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
class Category extends \MageWorx\SeoXTemplates\Model\AbstractTemplate
{
    /**
     * Cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'mageworx_seoxtemplates_template_category';

    /**
     * Type value for category meta title
     *
     * @var int
     */
    const TYPE_CATEGORY_META_TITLE       = 1;

    /**
     * Type value for category meta description
     *
     * @var int
     */
    const TYPE_CATEGORY_META_DESCRIPTION = 2;

    /**
     * Type value for category meta keywords
     *
     * @var int
     */
    const TYPE_CATEGORY_META_KEYWORDS    = 3;

    /**
     * Type value for category description
     *
     * @var int
     */
    const TYPE_CATEGORY_DESCRIPTION      = 4;

    /**
     * Type value for category SEO name
     *
     * @var int
     */
    const TYPE_CATEGORY_SEO_NAME         = 5;

    /**
     * Cache tag
     *
     * @var string
     */
    protected $cacheTag     = 'mageworx_seoxtemplates_template_category';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'mageworx_seoxtemplates_template_category';

    /**
     * @var HelperStore
     */
    protected $helperStore;

    /**
     *
     * @param CollectionFactory $categoryCollectionFactory
     * @param Context $context
     * @param Registry $registry
     * @param AttributeCodeOptions $attributeCodeOptions
     * @param AssignTypeOptions $assignTypeOptions
     * @param ScopeOptions $scopeOptions
     * @param IsUseCronOptions $isUseCronOptions
     * @param TypeOptions $typeOptions
     * @param HelperStore $helperStore
     * @param MagentoManagerInterface $messageManager
     * @param AbstractResource $resource
     * @param AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        CollectionFactory $categoryCollectionFactory,
        Context $context,
        Registry $registry,
        AttributeCodeOptions $attributeCodeOptions,
        AssignTypeOptions $assignTypeOptions,
        ScopeOptions $scopeOptions,
        IsUseCronOptions $isUseCronOptions,
        TypeOptions $typeOptions,
        HelperStore $helperStore,
        MagentoManagerInterface $messageManager,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {

        $this->attributeCodeOptions      = $attributeCodeOptions;
        $this->assignTypeOptions         = $assignTypeOptions;
        $this->scopeOptions              = $scopeOptions;
        $this->isUseCronOptions          = $isUseCronOptions;
        $this->typeOptions               = $typeOptions;
        $this->helperStore               = $helperStore;
        $this->categoryCollectionFactory = $categoryCollectionFactory;

        parent::__construct($context, $registry, $messageManager, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('MageWorx\SeoXTemplates\Model\ResourceModel\Template\Category');
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
     * Retrieve array template type ID => template title
     *
     * @return array
     */
    public function getTypeArray()
    {
        return $this->typeOptions->toArray();
    }

    /**
     * Retrieve type title such as "Category Meta Title", "Category SEO Name", ... by type ID
     *
     * @param int|null $typeId
     * @return string|null
     */
    public function getTypeTitleByType($typeId = null)
    {
        $typeId = $typeId ? $typeId : $this->getTypeId();
        $typeArray = $this->typeOptions->toArray();
        return empty($typeArray[$typeId]) ? null : $typeArray[$typeId];
    }

    /**
     * Retrieve attribute list for template type ID
     *
     * @param int|null $typeId
     * @return array|null
     */
    public function getAttributeCodesByType($typeId = null)
    {
        $typeId = $typeId ? $typeId : $this->getTypeId();
        $attributeCodeArray = $this->attributeCodeOptions->toArray();
        return empty($attributeCodeArray[$typeId]) ? null : $attributeCodeArray[$typeId];
    }

    /**
     * Retrieve default values for create
     *
     * @return array
     */
    public function getDefaultValuesForCreate()
    {
        return [
            'type_id'   => 0,
            'store_id'  => Store::DEFAULT_STORE_ID,
        ];
    }

    /**
     * Get default category template values for edit
     *
     * @return array
     */
    public function getDefaultValuesForEdit()
    {
        return [
            'is_use_cron' => self::CRON_DISABLED,
            'scopre'      => self::SCOPE_EMPTY,
            'assign_type' => self::ASSIGN_INDIVIDUAL_ITEMS,
        ];
    }

     /**
      * After save proccess
      *
      * @return $this
      */
    public function afterSave()
    {
        if ($this->isAssignForAllItems() && $this->getDuplicateTemplateAssignedForAll()) {
            $this->messageManager->addError(__('The template cannot be saved. There is another template assigned for all categories.'));
        }

        ///save related
        if ($this->getId() && $this->isAssignForAllItems()) {
            $this->_getResource()->clearAllRelations($this);
        } elseif ($this->getId() && $this->isAssignForIndividualItems()) {
            $oldAssignType = $this->getOrigData('assign_type');
            $newAssignType = $this->getData('assign_type');

            if ($oldAssignType == $newAssignType) {
                $itemIds       = $this->getCategoriesData() ? $this->getCategoriesData() : [];
                $oldItemIds    = $this->getOrigData('categories_data') ? $this->getOrigData('categories_data') : [];

                if ($oldItemIds == $itemIds) {
                    return parent::afterSave();
                }

                $analogItemIds = $this->getCategoryIdsAssignedForAnalogTemplate();
                if (array_intersect(array_map('intval', $itemIds), array_map('intval', $analogItemIds))) {
                    $this->messageManager->addError(__('The template was saved without assigned products. Please add categories manually.'));
                    return parent::afterSave();
                }
            }

            $this->_getResource()->saveCategoryRelation($this);
            $this->messageManager->addSuccess(__('Template "%1" was successfully saved', $this->getName()));
        }

        return parent::afterSave();
    }

    /**
     * Set related items
     *
     * @return void
     */
    public function loadItems()
    {
        if ($this->isAssignForIndividualItems()) {
            $itemIds = $this->_getResource()->getIndividualItemIds($this->getId());
            $this->setCategoriesData($itemIds);
        }
    }

    /**
     * Retrieve filtered collection for apply (or count)
     *
     * @param int $from
     * @param int $limit
     * @param bool $onlyCountFlag
     * @param int|null $nestedStoreId
     * @return \MageWorx\SeoXTemplates\Model\ResourceModel\Template\Category\Collection
     */
    public function getItemCollectionForApply($from, $limit, $onlyCountFlag = false, $nestedStoreId = null)
    {
        $microtime = microtime(1);

        $storeId = $this->getIsSingleStoreMode() ? $this->helperStore->getCurrentStoreId() : $this->getStoreId();

        if ($storeId === '0') {
            if ($this->issetUniqStoreTemplateForAllItems($nestedStoreId)) {
                return 0;
            }
            $excludeItemIds = $this->_getExcludeItemIdsByTemplate($nestedStoreId);
        } elseif ($storeId) {
            if ($this->isAssignForAllItems($this->getAssignType())) {
                $excludeItemIds = $this->_getExcludeItemIdsByTemplate();
            } else {
                $excludeItemIds = false;
            }
        }

        $storeId    = !empty($nestedStoreId) ? $nestedStoreId : $storeId;
        $collection = $this->categoryCollectionFactory->create();
        $collection->setStoreId($storeId);

        if (self::SCOPE_EMPTY == $this->getScope()) {
            $attributes = $this->getAttributeCodesByType();

            foreach ($attributes as $attributeCode) {
                $collection->addAttributeToSelect($attributeCode);
                $collection->addAttributeToFilter($attributeCode, ['null' => true], 'left');
            }
        }

        if ($this->isAssignForIndividualItems()) {
            $assignItems = (is_array($this->getCategoriesData()) && count($this->getCategoriesData())) ? $this->getCategoriesData() : 0;
            $collection->getSelect()->where('e.entity_id IN (?)', $assignItems);
        }
        if (!empty($excludeItemIds)) {
            $collection->getSelect()->where('e.entity_id NOT IN (?)', $excludeItemIds);
        }

        if ($onlyCountFlag) {
            return $collection->count();
        } else {
            $collection->addAttributeToSelect('*');
            $collection->getSelect()->order('e.entity_id asc');
            $collection->getSelect()->limit($limit, $from);

            return $collection;
        }
    }

    /**
     *
     * @return array
     */
    public function getCategoryIdsAssignedForAnalogTemplate()
    {
        $collection = $this->getCollection()
            ->addStoreModeFilter($this->getIsSingleStoreMode())
            ->addSpecificStoreFilter($this->getStoreId())
            ->addTypeFilter($this->getTypeId())
            ->addAssignTypeFilter(self::ASSIGN_INDIVIDUAL_ITEMS);
        if ($this->getTemplateId()) {
            $collection->excludeTemplateFilter($this->getTemplateId());
        }

        $templateIDs = $collection->getAllIds();
        return $this->_getResource()->getIndividualItemIds($templateIDs);
    }

    /**
     *
     * @param int $nestedStoreId
     * @return array|false
     */
    protected function _getExcludeItemIdsByTemplate($nestedStoreId = null)
    {
        $templateCollection = $this->getCollection()
            ->addStoreModeFilter($this->getIsSingleStoreMode())
            ->addTypeFilter($this->getTypeId())
            ->addAssignTypeFilter($this->getAssignForIndividualItems());

        if ($this->getStoreId() == '0' && !$this->getIsSingleStoreMode()) {
            if ($this->isAssignForAllItems($this->getAssignType())) {
                $templateCollection->addStoreFilter($nestedStoreId);
            } elseif ($this->isAssignForIndividualItems($this->getAssignType())) {
                $templateCollection->addStoreFilter($nestedStoreId);
                $templateCollection->excludeTemplateFilter($this->getTemplateId());
            }
        } elseif ($this->getStoreId() || $this->getIsSingleStoreMode()) {
            if ($this->isAssignForAllItems($this->getAssignType())) {
                $templateCollection->addSpecificStoreFilter($this->getStoreId());
            } elseif ($this->isAssignForIndividualItems($this->getAssignType())) {
                return false;
            }
        }

        $excludeItemIds = [];
        foreach ($templateCollection as $template) {
            $template->loadItems();
            if (!$template->isAssignForAllItems()) {
                $itemIds = $template->getCategoriesData();
                if (is_array($itemIds)) {
                    $excludeItemIds = array_merge($excludeItemIds, $itemIds);
                }
            }
        }
        return !empty($excludeItemIds) ? $excludeItemIds : false;
    }

    /**
     * Calc priority for template
     *
     * @return int
     */
    protected function calcPriority()
    {
        if ($this->getStoreId() == 0) {
            if ($this->getAssignType() == self::ASSIGN_ALL_ITEMS) {
                $priority = 1;
            } elseif ($this->getAssignType() == self::ASSIGN_INDIVIDUAL_ITEMS) {
                $priority = 2;
            }
        } else {
            if ($this->getAssignType() == self::ASSIGN_ALL_ITEMS) {
                $priority = 3;
            } elseif ($this->getAssignType() == self::ASSIGN_INDIVIDUAL_ITEMS) {
                $priority = 4;
            }
        }
        return $priority;
    }
}
