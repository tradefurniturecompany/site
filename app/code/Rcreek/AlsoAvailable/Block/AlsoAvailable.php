<?php

namespace Rcreek\AlsoAvailable\Block;

use Magento\Catalog\Model\Product;

class AlsoAvailable extends \Magento\Framework\View\Element\Template
{

    const MORE_COLLECTIONS_ID        = 1540;
    const MORE_COLLECTIONS_SUB_LEVEL = 3;

    /**
     * @var Product
     */
    protected $_product = null;

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
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        array $data = []
    )
    {
        $this->_coreRegistry = $registry;
        $this->productRepository = $productRepository;
        parent::__construct($context, $data);
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @return Product
     */
    public function getProduct()
    {
        if (!$this->_product) {
            $this->_product = $this->_coreRegistry->registry('product');
        }
        return $this->_product;
    }

    public function getLightUrl()
    {
        $product = $this->getProduct();
        if(!$this->checkSkuExists($product->getAlsoAvailableLightSku())) {
            return '';
        }

        if ($product->getAlsoAvailableLightSku()) {
            $l = $this->productRepository->get($product->getAlsoAvailableLightSku());
            if ($l) {
                return $l->getProductUrl();
            }
        }

        return '';
    }

    public function getDarkUrl()
    {
        $product = $this->getProduct();
        if(!$this->checkSkuExists($product->getAlsoAvailableDarkSku())) {
            return '';
        }

        if ($product->getAlsoAvailableDarkSku()) {
            $l = $this->productRepository->get($product->getAlsoAvailableDarkSku());
            if ($l) {
                return $l->getProductUrl();
            }
        }

        return '';
    }

    public function getNaturalUrl()
    {
        $product = $this->getProduct();
        if(!$this->checkSkuExists($product->getAlsoAvailableNaturalSku())) {
            return '';
        }

        if ($product->getAlsoAvailableNaturalSku()) {
            $l = $this->productRepository->get($product->getAlsoAvailableNaturalSku());
            if ($l) {
                return $l->getProductUrl();
            }
        }

        return '';
    }

    private function checkSkuExists($sku)
    {
        if (empty($sku)) {
            return false;
        }

        $searchCriteria = $this->searchCriteriaBuilder->addFilter("sku", $sku, 'eq')->create();
        $products = $this->productRepository->getList($searchCriteria);
        $items = $products->getItems();

        if (count($items) == 0) {
            return false;
        }

        return true;
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

    public function getTheRelevantMoreCollectionsSub()
    {
        $product = $this->getProduct();
        $categoryIds = $product->getCategoryIds();

        $categories = $this->getCategoryCollection(true, self::MORE_COLLECTIONS_SUB_LEVEL)
            ->addAttributeToFilter('entity_id', $categoryIds);

        foreach ($categories as $category) {
            if ($category->getParentId() == self::MORE_COLLECTIONS_ID) {
                return $category;
            }
        }

        return null;
    }
}
