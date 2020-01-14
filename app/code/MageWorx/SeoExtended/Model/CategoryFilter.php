<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoExtended\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Filter\FilterManager;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;
use MageWorx\SeoExtended\Helper\Data as HelperData;
use MageWorx\SeoAll\Helper\Category as HelperCategory;
use MageWorx\SeoExtended\Api\Data\CategoryFilterInterface;
use Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory as EavAttributeFactory;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\Collection as AttributeOptionCollection;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory as AttributeOptionCollectionFactory;

class CategoryFilter extends AbstractModel implements CategoryFilterInterface
{
    /**
     * Cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'mageworx_seoextended_categoryFilter';

    /**
     * cache tag
     *
     * @var string
     */
    protected $cacheTag = 'mageworx_seoextended_categoryFilter';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'mageworx_seoextended_categoryFilter';

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * @var EavAttributeFactory
     */
    protected $attributeModelFactory;

    /**
     * @var AttributeOptionCollection
     */
    protected $attributeOptionCollection;

    /**
     * @var string
     */
    protected $attributeLabel;

    /**
     * @var string
     */
    protected $attributeOptionLabel;

    /**
     * CategoryFilter constructor.
     *
     * @param FilterManager $filter
     * @param Context $context
     * @param Registry $registry
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param HelperData $helperData
     * @param HelperCategory $helperCategory
     * @param EavAttributeFactory $attributeModelFactory
     * @param AttributeOptionCollection $attributeOptionCollection
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        FilterManager $filter,
        Context $context,
        Registry $registry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        HelperData $helperData,
        HelperCategory $helperCategory,
        EavAttributeFactory $attributeModelFactory,
        AttributeOptionCollectionFactory $attributeOptionCollectionFactory,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->filter                    = $filter;
        $this->helperData                = $helperData;
        $this->helperCategory            = $helperCategory;
        $this->storeManager              = $storeManager;
        $this->attributeModelFactory     = $attributeModelFactory;
        $this->attributeOptionCollection = $attributeOptionCollectionFactory->create();
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('MageWorx\SeoExtended\Model\ResourceModel\CategoryFilter');
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
     * Get ID
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->getData(CategoryFilterInterface::CATEGORY_FILTER_ID);
    }


    /**
     * Get category ID
     *
     * @return int|null
     */
    public function getCategoryId()
    {
        return $this->getData(CategoryFilterInterface::CATEGORY_FILTER_CATEGORY_ID);
    }

    /**
     * Get attribute ID
     *
     * @return int|null
     */
    public function getAttributeId()
    {
        return $this->getData(CategoryFilterInterface::CATEGORY_FILTER_ATTRIBUTE_ID);
    }

    /**
     * Get attribute option ID
     *
     * @since 2.5.0
     * @return int|null
     */
    public function getAttributeOptionId()
    {
        return $this->getData(CategoryFilterInterface::CATEGORY_FILTER_ATTRIBUTE_OPTION_ID);
    }

    /**
     * Get store ID
     *
     * @return int|null
     */
    public function getStoreId()
    {
        return $this->getData(CategoryFilterInterface::CATEGORY_FILTER_STORE_ID);
    }

    /**
     * Get meta title
     *
     * @return string|null
     */
    public function getMetaTitle()
    {
        return $this->getData(CategoryFilterInterface::CATEGORY_FILTER_META_TITLE);
    }

    /**
     * Get meta description
     *
     * @return string|null
     */
    public function getMetaDescription()
    {
        return $this->getData(CategoryFilterInterface::CATEGORY_FILTER_META_DESCRIPTION);
    }

    /**
     * Get  description
     *
     * @return string|null
     */
    public function getDescription()
    {
        return $this->getData(CategoryFilterInterface::CATEGORY_FILTER_DESCRIPTION);
    }

    /**
     * Get meta keywords
     *
     * @return string|null
     */
    public function getMetaKeywords()
    {
        return $this->getData(CategoryFilterInterface::CATEGORY_FILTER_META_KEYWORDS);
    }

    /**
     * Set ID
     *
     * @param int $id
     * @return \MageWorx\SeoExtended\Api\Data\CategoryFilterInterface
     */
    public function setId($id)
    {
        return $this->setData(self::CATEGORY_FILTER_ID, $id);
    }

    /**
     * Set category ID
     *
     * @param int $categoryId
     * @return \MageWorx\SeoExtended\Api\Data\CategoryFilterInterface
     */
    public function setCategoryId($categoryId)
    {
        $this->setData(self::CATEGORY_FILTER_CATEGORY_ID, $categoryId);
    }

    /**
     * Set attribute ID
     *
     * @param int $attributeId
     * @return \MageWorx\SeoExtended\Api\Data\CategoryFilterInterface
     */
    public function setAttributeId($attributeId)
    {
        $this->setData(self::CATEGORY_FILTER_ATTRIBUTE_ID, $attributeId);
    }

    /**
     * Set attribute option ID
     *
     * @since 2.5.0
     * @param int $attributeOptionId
     * @return \MageWorx\SeoExtended\Api\Data\CategoryFilterInterface
     */
    public function setAttributeOptionId($attributeOptionId)
    {
        $this->setData(self::CATEGORY_FILTER_ATTRIBUTE_OPTION_ID, $attributeOptionId);
    }

    /**
     * Set store ID
     *
     * @param int $storeId
     * @return \MageWorx\SeoExtended\Api\Data\CategoryFilterInterface
     */
    public function setStoreId($storeId)
    {
        $this->setData(self::CATEGORY_FILTER_STORE_ID, $storeId);
    }

    /**
     * Set meta title
     *
     * @param string|null $metaTitle
     * @return \MageWorx\SeoExtended\Api\Data\CategoryFilterInterface
     */
    public function setMetaTitle($metaTitle)
    {
        $this->setData(self::CATEGORY_FILTER_META_TITLE, $metaTitle);
    }

    /**
     * Set meta description
     *
     * @param string|null $metaDescription
     * @return \MageWorx\SeoExtended\Api\Data\CategoryFilterInterface
     */
    public function setMetaDescription($metaDescription)
    {
        $this->setData(self::CATEGORY_FILTER_META_DESCRIPTION, $metaDescription);
    }

    /**
     * Set description
     *
     * @param string|null $description
     * @return \MageWorx\SeoExtended\Api\Data\CategoryFilterInterface
     */
    public function setDescription($description)
    {
        $this->setData(self::CATEGORY_FILTER_DESCRIPTION, $description);
    }

    /**
     * Set meta keywords
     *
     * @param string|null $metaKeywords
     * @return \MageWorx\SeoExtended\Api\Data\CategoryFilterInterface
     */
    public function setMetaLeywords($metaKeywords)
    {
        $this->setData(self::CATEGORY_FILTER_META_KEYWORDS, $metaKeywords);
    }

    /**
     * Get default category filter values
     * @return array
     */
    public function getDefaultValues()
    {
        return [];
    }

    protected function _afterLoad()
    {
        if ($this->getId()) {
            if ($this->getCategoryId()) {
                $names = implode('/', $this->helperCategory->getParentCategoryNamesById($this->getCategoryId(), 0));
                $this->setCategoryPathNames($names);
                $nameList = explode('/', $names);
                $name = array_pop($nameList);
                $this->setCategoryName($name);
            }

            if ($this->getAttributeId()) {
                $this->getAttributeLabel();
            }
        }

        parent::_afterLoad();
    }

    public function load($modelId, $field = null)
    {
        return parent::load($modelId, $field); // TODO: Change the autogenerated stub
    }

    /**
     *
     * @return \MageWorx\SeoExtended\Model\CategoryFilter
     */
    public function beforeSave()
    {
        return parent::beforeSave();
    }

    /**
     * @return mixed|string
     */
    public function getAttributeLabel()
    {
        if (!$this->attributeLabel) {
            $attributeModel = $this->attributeModelFactory->create();
            $attributeModel->load($this->getAttributeId());
            $this->attributeLabel = $attributeModel->getStoreLabel();
        }
        return $this->attributeLabel;
    }

    /**
     * @since 2.5.0
     * @return mixed|string
     */
    public function getAttributeOptionLabel()
    {
        if (!$this->attributeOptionLabel) {
            $this->attributeOptionLabel = $this->attributeOptionCollection
                ->setAttributeFilter($this->getAttributeId())
                ->setIdFilter($this->getAttributeOptionId())
                ->setStoreFilter(\Magento\Store\Model\Store::DEFAULT_STORE_ID)
                ->load()
                ->getFirstItem()
                ->getValue();
        }
        return $this->attributeOptionLabel;
    }

    /**
     * @return array
     */
    public function getMarkedCategories()
    {
        if ($this->getStoreId() && $this->getAttributeId()) {
            /** @var \MageWorx\SeoExtended\Model\ResourceModel\CategoryFilter\Collection $collection */
            $collection = $this->getCollection();
            $collection->addFieldToFilter('store_id', $this->getStoreId());
            $collection->addFieldToFilter('attribute_id', $this->getAttributeId());
            $collection->addFieldToSelect('category_id', $this->getCategoryId());

            return $collection->getColumnValues('category_id');
        }
        return [];
    }
}
