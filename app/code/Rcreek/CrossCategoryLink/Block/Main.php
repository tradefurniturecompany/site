<?php

namespace Rcreek\CrossCategoryLink\Block;

use Magento\Catalog\Model\Category;

class Main extends \Magento\Framework\View\Element\Template
{

    const MORE_COLLECTIONS_ID = 1540;
    const MORE_COLLECTIONS_SUB_LEVEL = 3;

    /**
     * @var Category
     */
    protected $_category = null;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;
    protected $productRepository;
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    private $categoryCollectionFactory;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        array $data = []
    )
    {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
        $this->categoryCollectionFactory = $categoryCollectionFactory;
    }

    /**
     * @return Category
     */
    public function getCategory()
    {
        if (!$this->_category) {
            $this->_category = $this->_coreRegistry->registry('current_category');
        }
        return $this->_category;
    }

    /**
     * Get category collection
     *
     * @param bool $isActive
     * @param bool|int $level
     * @param bool|string $sortBy
     * @param bool|int $pageSize
     * @return \Magento\Catalog\Model\ResourceModel\Category\Collection or array
     */
    public function getCategoryCollection($isActive = true, $level = false, $sortBy = false, $pageSize = false)
    {
        $collection = $this->categoryCollectionFactory->create();
        $collection->addAttributeToSelect('*');

        // select only active categories
        if ($isActive) {
            $collection->addIsActiveFilter();
        }

        // select categories of certain level
        if ($level) {
            $collection->addLevelFilter($level);
        }

        // sort categories by some value
        if ($sortBy) {
            $collection->addOrderField($sortBy);
        }

        // select certain number of categories
        if ($pageSize) {
            $collection->setPageSize($pageSize);
        }

        return $collection;
    }

    public function getLinkObject()
    {
        $currentCategory = $this->getCategory();

        if(!$currentCategory || $currentCategory->getLevel() != 3 || $currentCategory->getParentId() == self::MORE_COLLECTIONS_ID) {
            return null;
        }

        $linkObject = new \stdClass();
        $linkObject->text = "View More '" . $currentCategory->getName() . "'";

        $categories = $this->getCategoryCollection(true, self::MORE_COLLECTIONS_SUB_LEVEL)
            ->addAttributeToFilter('parent_id', self::MORE_COLLECTIONS_ID);

        foreach ($categories as $c) {
            if($c->getName() === $currentCategory->getName()) {
                $linkObject->url = $c->getUrl();
                return $linkObject;
            }
        }

        return null;
    }
}
