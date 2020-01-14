<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Model\Template;

use Braintree\Exception;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Store\Model\Store;
use MageWorx\SeoXTemplates\Model\Template\CategoryFilter\Source\AttributeCode as AttributeCodeOptions;
use MageWorx\SeoXTemplates\Model\Template\Source\AssignType as AssignTypeOptions;
use MageWorx\SeoXTemplates\Model\Template\Source\Scope as ScopeOptions;
use MageWorx\SeoXTemplates\Model\Template\Source\IsUseCron as IsUseCronOptions;
use MageWorx\SeoXTemplates\Model\Template\Category\Source\Type as TypeOptions;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Framework\Message\ManagerInterface as MagentoManagerInterface;

/**
 * @method int getTypeId()
 * @method Template setTypeId(\int $title)
 * @method int getAttributeId()
 * @method Template setAttributeId(\int $attributeId)
 * @method int getAttributeOptionId()
 * @method Template setAttributeOptionId(\int $attributeOptionId)
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
 */
class CategoryFilter extends \MageWorx\SeoXTemplates\Model\AbstractTemplate
{
    /**
     * Cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'mageworx_seoxtemplates_template_categoryfilter';

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
     * Type value for category name
     *
     * @var int
     */
    const TYPE_CATEGORY_NAME             = 5;

    /**
     * Cache tag
     *
     * @var string
     */
    protected $cacheTag     = 'mageworx_seoxtemplates_template_categoryfilter';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'mageworx_seoxtemplates_template_categoryfilter';

    /**
     * @var \MageWorx\SeoExtended\Model\ResourceModel\CategoryFilter\CollectionFactory
     */
    protected $categoryFilterCollectionFactory;


    /**
     * CategoryFilter constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param AttributeCodeOptions $attributeCodeOptions
     * @param AssignTypeOptions $assignTypeOptions
     * @param ScopeOptions $scopeOptions
     * @param IsUseCronOptions $isUseCronOptions
     * @param TypeOptions $typeOptions
     * @param MagentoManagerInterface $messageManager
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        AttributeCodeOptions $attributeCodeOptions,
        AssignTypeOptions $assignTypeOptions,
        ScopeOptions $scopeOptions,
        IsUseCronOptions $isUseCronOptions,
        TypeOptions $typeOptions,
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

        if (empty($data['category_filter_collection_factory'])) {
            throw new \UnexpectedValueException(
                'SEO Category Filter Collection is not exists.'
            );
        }

        $this->categoryFilterCollectionFactory = $data['category_filter_collection_factory'];

        parent::__construct($context, $registry, $messageManager, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('MageWorx\SeoXTemplates\Model\ResourceModel\Template\CategoryFilter');
    }

    /**
     * @return \MageWorx\SeoExtended\Model\ResourceModel\CategoryFilter\CollectionFactory
     */
    public function getCategoryFilterCollectionFactory()
    {
        return $this->categoryFilterCollectionFactory;
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
            $this->messageManager->addErrorMessage(__('The template cannot be saved. There is another template assigned for all categories.'));
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
                    $this->messageManager->addErrorMessage(__('The template was saved without assigned category. Please add categories manually.'));
                    return parent::afterSave();
                }
            }

            $this->_getResource()->saveCategoryRelation($this);
            $this->messageManager->addSuccessMessage(__('Template "%1" was successfully saved', $this->getName()));
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
     * @param int $from
     * @param int $limit
     * @param bool $onlyCountFlag
     * @param null|int $nestedStoreId
     * @return int|\MageWorx\SeoExtended\Model\ResourceModel\CategoryFilter\Collection
     */
    public function getItemCollectionForApply($from, $limit, $onlyCountFlag = false, $nestedStoreId = null)
    {
        if ($this->getStoreId() === '0') {
            if ($this->issetUniqStoreTemplateForAllItems($nestedStoreId)) {
                return 0;
            }
            $excludeItemIds = $this->_getExcludeItemIdsByTemplate($nestedStoreId);
        } elseif ($this->getStoreId()) {
            if ($this->isAssignForAllItems($this->getAssignType())) {
                $excludeItemIds = $this->_getExcludeItemIdsByTemplate($nestedStoreId);
            } else {
                $excludeItemIds = false;
            }
        }

        $storeId = !empty($nestedStoreId) ? $nestedStoreId : $this->getStoreId();

        /** @var \MageWorx\SeoExtended\Model\ResourceModel\CategoryFilter\Collection $collection */
        $collection = $this->categoryFilterCollectionFactory->create();
        $collection->addFieldToFilter('store_id', $storeId);
        $collection->addFieldToFilter('attribute_id', $this->getAttributeId());
        $collection->addFieldToFilter('attribute_option_id', $this->getAttributeOptionId());

        if (self::SCOPE_EMPTY == $this->getScope()) {
            $attributes = $this->getAttributeCodesByType();

            foreach ($attributes as $attributeCode) {
                $collection->addFieldToFilter($attributeCode, '');
            }
        }

        if ($this->isAssignForIndividualItems()) {
            $assignItems = (is_array($this->getCategoriesData()) && count($this->getCategoriesData())) ? $this->getCategoriesData() : 0;
            $collection->getSelect()->where('category_id IN (?)', $assignItems);
        }
        if (!empty($excludeItemIds)) {
            $collection->getSelect()->where('category_id NOT IN (?)', $excludeItemIds);
        }
        
        if ($onlyCountFlag) {
            return $collection->count();
        }

        $collection->getSelect()->limit($limit, $from);

        return $collection;
    }

    /**
     * Check if the template that is assigned to all items already exists
     *
     * @param int $nestedStoreId
     * @return boolean
     */
    protected function issetUniqStoreTemplateForAllItems($nestedStoreId)
    {
        if ($this->getStoreId() == '0') {

            /** @var \MageWorx\SeoXTemplates\Model\ResourceModel\Template\CategoryFilter\Collection $templateCollection */
            $templateCollection = $this->getCollection();
            $templateCollection
                ->addTypeFilter($this->getTypeId())
                ->addAttributeFilter($this->getAttributeId())
                ->addAssignTypeFilter(self::ASSIGN_ALL_ITEMS)
                ->addSpecificStoreFilter($nestedStoreId);

            if ($templateCollection->count()) {
                return true;
            }
        }
        return false;
    }

    /**
     *
     * @return array
     */
    public function getCategoryIdsAssignedForAnalogTemplate()
    {
        /** @var \MageWorx\SeoXTemplates\Model\ResourceModel\Template\CategoryFilter\Collection $collection */
        $collection = $this->getCollection()
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
        /** @var \MageWorx\SeoXTemplates\Model\ResourceModel\Template\CategoryFilter\Collection $templateCollection */
        $templateCollection = $this->getCollection();
        $templateCollection->addTypeFilter($this->getTypeId());
        $templateCollection->addAssignTypeFilter($this->getAssignForIndividualItems());
        $templateCollection->addAttributeFilter($this->getAttributeId());

        if ($this->getStoreId() == '0') {
            if ($this->isAssignForAllItems($this->getAssignType())) {
                $templateCollection->addStoreFilter($nestedStoreId);
            } elseif ($this->isAssignForIndividualItems($this->getAssignType())) {
                $templateCollection->addStoreFilter($nestedStoreId);
                $templateCollection->excludeTemplateFilter($this->getTemplateId());
            }
        } elseif ($this->getStoreId()) {
            if ($this->isAssignForAllItems($this->getAssignType())) {
                $templateCollection->addSpecificStoreFilter($this->getStoreId());
            } elseif ($this->isAssignForIndividualItems($this->getAssignType())) {
                return false;
            }
        }

        $excludeItemIds = array();
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
     * Retrieve duplicate template that is assigned to all items
     *
     * @return $this|false
     */
    public function getDuplicateTemplateAssignedForAll()
    {
        $templateCollection = $this->getCollection()
            ->addSpecificStoreFilter($this->getStoreId())
            ->addTypeFilter($this->getTypeId())
            ->addAttributeFilter($this->getAttributeId())
            ->addAssignTypeFilter(self::ASSIGN_ALL_ITEMS);
        if ($this->getTemplateId()) {
            $templateCollection->excludeTemplateFilter($this->getTemplateId());
        }

        if ($templateCollection->count()) {
            return $templateCollection->getFirstItem();
        }
        return false;
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