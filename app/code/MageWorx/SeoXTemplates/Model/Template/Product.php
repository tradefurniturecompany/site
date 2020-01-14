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
use MageWorx\SeoXTemplates\Model\Template\Product\Source\AttributeCode as AttributeCodeOptions;
use MageWorx\SeoXTemplates\Model\Template\Source\AssignType as AssignTypeOptions;
use MageWorx\SeoXTemplates\Model\Template\Source\Scope as ScopeOptions;
use MageWorx\SeoXTemplates\Model\Template\Source\IsUseCron as IsUseCronOptions;
use MageWorx\SeoXTemplates\Model\Template\Product\Source\Type as TypeOptions;
use MageWorx\SeoXTemplates\Helper\Store as HelperStore;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\Message\ManagerInterface as MagentoManagerInterface;
use MageWorx\SeoXTemplates\Model\DataProviderProductFactory;

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
class Product extends \MageWorx\SeoXTemplates\Model\AbstractTemplate
{
    /**
     * Cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'mageworx_seoxtemplates_template_product';

    /**
     * Assign type value for group items (attribute sets)
     *
     * @var int
     */
    const ASSIGN_GROUP_ITEMS = '3';

    /**
     * Type value for product name
     *
     * @var int
     */
    const TYPE_PRODUCT_SEO_NAME = '1';

    /**
     * Type value for product URL key
     *
     * @var int
     */
    const TYPE_PRODUCT_URL_KEY = '2';

    /**
     * Type value for product short description
     *
     * @var int
     */
    const TYPE_PRODUCT_SHORT_DESCRIPTION = '3';

    /**
     * Type value for product description
     *
     * @var int
     */
    const TYPE_PRODUCT_DESCRIPTION = '4';

    /**
     * Type value for product meta title
     *
     * @var int
     */
    const TYPE_PRODUCT_META_TITLE = '5';

    /**
     * Type value for product meta description
     *
     * @var int
     */
    const TYPE_PRODUCT_META_DESCRIPTION = '6';

    /**
     * Type value for product meta keywords
     *
     * @var int
     */
    const TYPE_PRODUCT_META_KEYWORDS = '7';

    /**
     * Type value for product meta keywords
     *
     * @var int
     */
    const TYPE_PRODUCT_GALLERY = '8';

    /**
     * Cache tag
     *
     * @var string
     */
    protected $cacheTag = 'mageworx_seoxtemplates_template_product';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'mageworx_seoxtemplates_template_product';

    /**
     *
     * @var AttributeCodeOptions
     */
    protected $attributeCodeOptions;

    /**
     *
     * @var AssignTypeOptions
     */
    protected $assignTypeOptions;

    /**
     *
     * @var ScopeOptions
     */
    protected $scopeOptions;

    /**
     *
     * @var IsUseCronOptions
     */
    protected $isUseCronOptions;

    /**
     *
     * @var TypeOptions
     */
    protected $typeOptions;

    /**
     * @var HelperStore
     */
    protected $helperStore;

    /**
     *
     * @var \MageWorx\SeoXTemplates\Model\DataProviderProductFactory
     */
    protected $dataProviderProductFactory;

    /**
     *
     * @param CollectionFactory $productCollectionFactory
     * @param MagentoManagerInterface $messageManager
     * @param Context $context
     * @param Registry $registry
     * @param AttributeCodeOptions $attributeCodeOptions
     * @param AssignTypeOptions $assignTypeOptions
     * @param ScopeOptions $scopeOptions
     * @param IsUseCronOptions $isUseCronOptions
     * @param TypeOptions $typeOptions
     * @param HelperStore $helperStore
     * @param AbstractResource $resource
     * @param AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        CollectionFactory $productCollectionFactory,
        MagentoManagerInterface $messageManager,
        Context $context,
        Registry $registry,
        AttributeCodeOptions $attributeCodeOptions,
        AssignTypeOptions $assignTypeOptions,
        ScopeOptions $scopeOptions,
        IsUseCronOptions $isUseCronOptions,
        TypeOptions $typeOptions,
        HelperStore $helperStore,
        DataProviderProductFactory $dataProviderProductFactory,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->attributeCodeOptions       = $attributeCodeOptions;
        $this->assignTypeOptions          = $assignTypeOptions;
        $this->scopeOptions               = $scopeOptions;
        $this->isUseCronOptions           = $isUseCronOptions;
        $this->typeOptions                = $typeOptions;
        $this->helperStore                = $helperStore;
        $this->productCollectionFactory   = $productCollectionFactory;
        $this->dataProviderProductFactory = $dataProviderProductFactory;

        parent::__construct($context, $registry, $messageManager, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('MageWorx\SeoXTemplates\Model\ResourceModel\Template\Product');
    }

    /**
     *
     * @param int|null $assignType
     * @return bool
     */
    public function isAssignForGroupItems($assignType = null)
    {
        $assignType = $assignType ? $assignType : $this->getAssignType();

        return self::ASSIGN_GROUP_ITEMS == $assignType;
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
     * Retrieve type title such as "Product URL Key", "Product Meta Title", ... by type ID
     *
     * @param int|null $typeId
     * @return string|null
     */
    public function getTypeTitleByType($typeId = null)
    {
        $typeId    = $typeId ? $typeId : $this->getTypeId();
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
        $typeId             = $typeId ? $typeId : $this->getTypeId();
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
            'type_id'  => 0,
            'store_id' => Store::DEFAULT_STORE_ID,
        ];
    }

    /**
     * Get default product template values for edit
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
            $this->messageManager->addErrorMessage(
                __('The template cannot be saved. There is another template assigned for all products.')
            );
        }

        ///save related
        if ($this->getId() && $this->isAssignForAllItems()) {
            $this->_getResource()->clearAllRelations($this);
        } elseif ($this->getId() && $this->isAssignForIndividualItems()) {
            $oldAssignType = $this->getOrigData('assign_type');
            $newAssignType = $this->getData('assign_type');

            if ($oldAssignType == $newAssignType) {
                $itemIds    = $this->getProductsData() ? $this->getProductsData() : [];
                $oldItemIds = $this->getOrigData('products_data') ? $this->getOrigData('product_data') : [];

                if ($oldItemIds == $itemIds) {
                    return parent::afterSave();
                }

                $analogItemIds = $this->getProductIdsAssignedForAnalogTemplate();
                if (array_intersect(array_map('intval', $itemIds), array_map('intval', $analogItemIds))) {
                    $this->messageManager->addErrorMessage(
                        __('The template was saved without assigned products. Please add products manually.')
                    );

                    return parent::afterSave();
                }
            }

            $this->_getResource()->saveProductRelation($this);
            $this->messageManager->addSuccessMessage(__('Template "%1" was successfully saved', $this->getName()));
        } elseif ($this->getId() && $this->isAssignForGroupItems()) {
            $itemIds       = $this->getAttributesetData() ? $this->getAttributesetData() : [];
            $analogItemIds = $this->getAttributesetIdsAssignedForAnalogTemplate();

            if (array_intersect(array_map('intval', $itemIds), array_map('intval', $analogItemIds))) {
                $this->messageManager->addErrorMessage(
                    __('The template was saved without assigned products. Please add products manually.')
                );

                return parent::afterSave();
            }

            $this->_getResource()->saveAttributesetRelation($this);
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
            $this->setProductData($itemIds);
        } elseif ($this->isAssignForGroupItems()) {
            $groupItemId = $this->_getResource()->getGroupItemId($this->getId());
            $this->setAttributeset($groupItemId);

            if (!$groupItemId) {
                return $this;
            }

            $productCollection = $this->productCollectionFactory->create();
            $itemIds           = $productCollection->addFieldToFilter('attribute_set_id', $groupItemId)->getAllIds();
            $this->setProductData($itemIds);
        }
    }

    /**
     * Retrieve filtered collection for apply (or count)
     *
     * @param int|null $from
     * @param int|null $limit
     * @param bool $onlyCountFlag
     * @param int|null $nestedStoreId
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection|false
     */
    public function getItemCollectionForApply(
        $from = null,
        $limit = null,
        $onlyCountFlag = false,
        $nestedStoreId = null
    ) {
        $microtime = microtime(1);

        $storeId = $this->getIsSingleStoreMode() ? $this->helperStore->getCurrentStoreId() : $this->getStoreId();

        if ($storeId == '0') {
            if ($this->issetUniqStoreTemplateForAllItems($nestedStoreId)) {
                return false;
            }

            if ($this->isAssignForIndividualItems()) {
                $excludeItemIds = $this->_getExcludeItemIdsByTemplate($nestedStoreId);
                $assignItems    = $this->getProductData();
            } elseif ($this->isAssignForGroupItems()) {
                $excludeItemIds = $this->_getExcludeItemIdsByTemplate($nestedStoreId);
                $assignItems    = $this->getProductData();
            } elseif ($this->isAssignForAllItems()) {
                $excludeItemIds = $this->_getExcludeItemIdsByTemplate($nestedStoreId);
            }
        } elseif ($storeId) {
            if ($this->isAssignForIndividualItems()) {
                $excludeItemIds = false;
                $assignItems    = $this->getProductData();
                $assignItems    = (is_array($assignItems) && count($assignItems)) ? $assignItems : false;
            } elseif ($this->isAssignForGroupItems()) {
                $excludeItemIds = $this->_getExcludeItemIdsByTemplate();
                $assignItems    = $this->getProductData();
                $assignItems    = (is_array($assignItems) && count($assignItems)) ? $assignItems : false;
            } elseif ($this->isAssignForAllItems()) {
                $excludeItemIds = $this->_getExcludeItemIdsByTemplate();
            }
        }

        $storeId = !empty($nestedStoreId) ? $nestedStoreId : $storeId;

        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $collection */
        $collection = $this->productCollectionFactory->create();
        $collection->addStoreFilter($storeId);
        $collection->setStoreId($storeId);
//        $collection->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH);

        /** @var \MageWorx\SeoXTemplates\Model\DataProvider $dataProvider */
        $dataProvider = $this->dataProviderProductFactory->create($this->getTypeId());
        $dataProvider->addFiltersToEntityCollection($this, $collection);

        if (isset($assignItems)) {
            if (is_array($assignItems) && count($assignItems)) {
                if (is_array($excludeItemIds) && count($excludeItemIds)) {
                    $assignItems = array_diff(array_map('intval', $assignItems), array_map('intval', $excludeItemIds));
                    if (!count($assignItems)) {
                        return false;
                    }
                }
                $collection->getSelect()->where('e.entity_id IN (?)', $assignItems);
            } else {
                return false;
            }
        } else {
            if (is_array($excludeItemIds) && count($excludeItemIds)) {
                $collection->getSelect()->where('e.entity_id NOT IN (?)', $excludeItemIds);
            }
        }

        if ($onlyCountFlag) {
            return $collection->count();
        }

        $collection->addAttributeToSelect('*');
        $collection->getSelect()->order('e.entity_id asc');
        $collection->getSelect()->limit($limit, $from);

        $dataProvider->onLoadEntityCollection($this, $collection);

        return $collection;
    }

    /**
     *
     * @return array
     */
    public function getProductIdsAssignedForAnalogTemplate()
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
     * @return array
     */
    public function getAttributesetIdsAssignedForAnalogTemplate()
    {
        $collection = $this->getCollection()
                           ->addStoreModeFilter($this->getIsSingleStoreMode())
                           ->addSpecificStoreFilter($this->getStoreId())
                           ->addTypeFilter($this->getTypeId())
                           ->addAssignTypeFilter(self::ASSIGN_GROUP_ITEMS);

        if ($this->getTemplateId()) {
            $collection->excludeTemplateFilter($this->getTemplateId());
        }

        $templateIDs = $collection->getAllIds();

        return $this->_getResource()->getGroupItemId($templateIDs);
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
                                   ->addTypeFilter($this->getTypeId());

        if ($this->getStoreId() == '0' && !$this->getIsSingleStoreMode()) {
            if ($this->isAssignForIndividualItems()) {
                $templateCollection->addSpecificStoreFilter($nestedStoreId);
                $templateCollection->addAssignTypeFilter([self::ASSIGN_INDIVIDUAL_ITEMS, self::ASSIGN_GROUP_ITEMS]);
            } elseif ($this->isAssignForGroupItems()) {
                $templateCollection->addStoreFilter($nestedStoreId);
                $templateCollection->addAssignTypeFilter([self::ASSIGN_INDIVIDUAL_ITEMS, self::ASSIGN_GROUP_ITEMS]);
                $templateCollection->excludeTemplateFilter($this->getTemplateId());
            } elseif ($this->isAssignForAllItems()) {
                $templateCollection->addStoreFilter($nestedStoreId);
                $templateCollection->addAssignTypeFilter([self::ASSIGN_INDIVIDUAL_ITEMS, self::ASSIGN_GROUP_ITEMS]);
            }
        } elseif ($this->getStoreId() || $this->getIsSingleStoreMode()) {
            if ($this->isAssignForIndividualItems()) {
                return false;
            } elseif ($this->isAssignForGroupItems()) {
                $templateCollection->addSpecificStoreFilter($this->getStoreId());
                $templateCollection->addAssignTypeFilter(self::ASSIGN_INDIVIDUAL_ITEMS);
            } elseif ($this->isAssignForAllItems()) {
                $templateCollection->addSpecificStoreFilter($this->getStoreId());
                $templateCollection->addAssignTypeFilter([self::ASSIGN_INDIVIDUAL_ITEMS, self::ASSIGN_GROUP_ITEMS]);
            }
        }

        $excludeItemIds = [];

        foreach ($templateCollection as $template) {
            $template->loadItems();
            if (!$template->isAssignForAllItems()) {
                $itemIds = $template->getProductData();
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
            if ($this->isAssignForAllItems()) {
                $priority = 1;
            } elseif ($this->isAssignForGroupItems()) {
                $priority = 2;
            } elseif ($this->isAssignForIndividualItems()) {
                $priority = 3;
            }
        } else {
            if ($this->isAssignForAllItems()) {
                $priority = 4;
            } elseif ($this->isAssignForGroupItems()) {
                $priority = 5;
            } elseif ($this->isAssignForIndividualItems()) {
                $priority = 6;
            }
        }

        return $priority;
    }

    public function getAttributesetValue()
    {
        $attributeSet = $this->getData('attributeset');

        if (is_array($attributeSet)) {
            return array_shift($attributeSet);
        }

        return $attributeSet;
    }
}
