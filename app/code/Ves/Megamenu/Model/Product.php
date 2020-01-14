<?php
/**
 * Venustheme
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Venustheme.com license that is
 * available through the world-wide-web at this URL:
 * http://www.venustheme.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Venustheme
 * @package    Ves_Megamenu
 * @copyright  Copyright (c) 2016 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */
namespace Ves\Megamenu\Model;

class Product extends \Magento\Framework\DataObject
{
	/**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * @var \Magento\Reports\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_reportCollection;

    /**
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $_catalogProductVisibility;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_localeDate;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

	/**
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory 
     * @param \Magento\Reports\Model\ResourceModel\Product\CollectionFactory $reportCollection         
     * @param \Magento\Catalog\Model\Product\Visibility                 $catalogProductVisibility 
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface      $localeDate               
     * @param \Magento\Store\Model\StoreManagerInterface                $storeManager             
     * @param array                                                     $data                     
     */
	public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Reports\Model\ResourceModel\Product\CollectionFactory $reportCollection,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = []
        ) {
        $this->_localeDate = $localeDate;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_reportCollection = $reportCollection;
        $this->_catalogProductVisibility = $catalogProductVisibility;
        $this->_storeManager = $storeManager;
        parent::__construct($data);
    }

	/**
     * New arrival product collection
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection|Object|\Magento\Framework\Data\Collection
     */
    public function getNewarrivalProducts()
    {
    	$todayStartOfDayDate = $this->_localeDate->date()->setTime(0, 0, 0)->format('Y-m-d H:i:s');
        $todayEndOfDayDate = $this->_localeDate->date()->setTime(23, 59, 59)->format('Y-m-d H:i:s');

        /** @var $collection \Magento\Catalog\Model\ResourceModel\Product\Collection */
        $collection = $this->_productCollectionFactory->create()->addAttributeToSelect('*');
        if(is_array($config['cats']) && !empty($config['cats'])){
            $collection->addFieldToFilter('visibility', array(
               \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH,
               \Magento\Catalog\Model\Product\Visibility::VISIBILITY_IN_CATALOG
               ))
            ->addMinimalPrice()
            ->addUrlRewrite()
            ->addTaxPercents()
            ->addStoreFilter()
            ->addFinalPrice();
            $collection ->joinTable(
                'catalog_category_product',
                'product_id=entity_id', 
                array('category_id'=>'category_id'), 
                null, 
                'left')
            ->addAttributeToFilter( array( array('attribute' => 'category_id', 'in' => array('finset' => $config['cats']))))
            ->groupByAttribute('entity_id');
        }
        $collection->addStoreFilter()->addAttributeToFilter(
            'news_from_date',
            [
            'or' => [
            0 => ['date' => true, 'to' => $todayEndOfDayDate],
            1 => ['is' => new \Zend_Db_Expr('null')],
            ]
            ],
            'left'
            )->addAttributeToFilter(
            'news_to_date',
            [
            'or' => [
            0 => ['date' => true, 'from' => $todayStartOfDayDate],
            1 => ['is' => new \Zend_Db_Expr('null')],
            ]
            ],
            'left'
            )->addAttributeToFilter(
            [
            ['attribute' => 'news_from_date', 'is' => new \Zend_Db_Expr('not null')],
            ['attribute' => 'news_to_date', 'is' => new \Zend_Db_Expr('not null')],
            ]
            )->addAttributeToSort(
            'news_from_date',
            'desc'
            )
            ->setPageSize(isset($config['pagesize'])?$config['pagesize']:5)
            ->setCurPage(isset($config['curpage'])?$config['curpage']:1);
            return $collection;
        }

    /**
     * Latest product collection
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection|Object|\Magento\Framework\Data\Collection
     */
    public function getLatestProducts($config = array())
    {
        /** @var $collection \Magento\Catalog\Model\ResourceModel\Product\Collection */
        $collection = $this->_productCollectionFactory->create()->addAttributeToSelect('*');
        if(is_array($config['cats']) && !empty($config['cats'])){
            $collection->addFieldToFilter('visibility', array(
               \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH,
               \Magento\Catalog\Model\Product\Visibility::VISIBILITY_IN_CATALOG
               ))
            ->addMinimalPrice()
            ->addUrlRewrite()
            ->addTaxPercents()
            ->addStoreFilter()
            ->addFinalPrice();
            $collection ->joinTable(
                'catalog_category_product',
                'product_id=entity_id', 
                array('category_id'=>'category_id'), 
                null, 
                'left')
            ->addAttributeToFilter( array( array('attribute' => 'category_id', 'in' => array('finset' => $config['cats']))))
            ->groupByAttribute('entity_id');
        }
        $collection->addStoreFilter()
        ->setPageSize(isset($config['pagesize'])?$config['pagesize']:5)
        ->setCurPage(isset($config['curpage'])?$config['curpage']:1);
        return $collection;
    }

    /**
     * Best seller product collection
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection|Object|\Magento\Framework\Data\Collection
     */
    public function getBestsellerProducts($config = array())
    {
        $storeId = $this->_storeManager->getStore(true)->getId();
        /** @var $collection \Magento\Catalog\Model\ResourceModel\Product\Collection */
        $collection = $this->_productCollectionFactory->create()->addAttributeToSelect('*');
        if(is_array($config['cats']) && !empty($config['cats'])){
            $collection->addFieldToFilter('visibility', array(
               \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH,
               \Magento\Catalog\Model\Product\Visibility::VISIBILITY_IN_CATALOG
               ))
            ->addMinimalPrice()
            ->addUrlRewrite()
            ->addTaxPercents()
            ->addStoreFilter()
            ->addFinalPrice();
            $collection ->joinTable(
                'catalog_category_product',
                'product_id=entity_id', 
                array('category_id'=>'category_id'), 
                null, 
                'left')
            ->addAttributeToFilter( array( array('attribute' => 'category_id', 'in' => array('finset' => $config['cats']))))
            ->groupByAttribute('entity_id');
        }
        $collection->addStoreFilter()
        ->joinField(
            'qty_ordered',
            'sales_bestsellers_aggregated_monthly',
            'qty_ordered',
            'product_id=entity_id',
            'at_qty_ordered.store_id=' . (int)$storeId,
            'at_qty_ordered.qty_ordered > 0',
            'left'
            )
        ->setPageSize(isset($config['pagesize'])?$config['pagesize']:5)
        ->setCurPage(isset($config['curpage'])?$config['curpage']:1);
        return $collection;
    }

    /**
     * Random product collection
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection|Object|\Magento\Framework\Data\Collection
     */
    public function getRandomProducts($config = array())
    {
        /** @var $collection \Magento\Catalog\Model\ResourceModel\Product\Collection */
        $collection = $this->_productCollectionFactory->create()->addAttributeToSelect('*');
        if(is_array($config['cats']) && !empty($config['cats'])){
            $collection->addFieldToFilter('visibility', array(
               \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH,
               \Magento\Catalog\Model\Product\Visibility::VISIBILITY_IN_CATALOG
               ))
            ->addMinimalPrice()
            ->addUrlRewrite()
            ->addTaxPercents()
            ->addStoreFilter()
            ->addFinalPrice();
            $collection ->joinTable(
                'catalog_category_product',
                'product_id=entity_id', 
                array('category_id'=>'category_id'), 
                null, 
                'left')
            ->addAttributeToFilter( array( array('attribute' => 'category_id', 'in' => array('finset' => $config['cats']))))
            ->groupByAttribute('entity_id');
        }
        $collection->addStoreFilter()
        ->setPageSize(isset($config['pagesize'])?$config['pagesize']:5)
        ->setCurPage(isset($config['curpage'])?$config['curpage']:1);
        $collection->getSelect()->order('rand()');
        return $collection;
    }

    /**
     * Top rated product collection
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection|Object|\Magento\Framework\Data\Collection
     */
    public function getTopratedProducts($config = array())
    {
        $storeId = $this->_storeManager->getStore(true)->getId();
        /** @var $collection \Magento\Catalog\Model\ResourceModel\Product\Collection */
        $collection = $this->_productCollectionFactory->create()->addAttributeToSelect('*');
        if(is_array($config['cats']) && !empty($config['cats'])){
            $collection->addFieldToFilter('visibility', array(
               \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH,
               \Magento\Catalog\Model\Product\Visibility::VISIBILITY_IN_CATALOG
               ))
            ->addMinimalPrice()
            ->addUrlRewrite()
            ->addTaxPercents()
            ->addStoreFilter()
            ->addFinalPrice();
            $collection ->joinTable(
                'catalog_category_product',
                'product_id=entity_id', 
                array('category_id'=>'category_id'), 
                null, 
                'left')
            ->addAttributeToFilter( array( array('attribute' => 'category_id', 'in' => array('finset' => $config['cats']))))
            ->groupByAttribute('entity_id');
        }
        $collection->addStoreFilter()
        ->joinField(
            'ves_review',
            'review_entity_summary',
            'reviews_count',
            'entity_pk_value=entity_id',
            'at_ves_review.store_id=' . (int)$storeId,
            'ves_review > 0',
            'left'
            )
        ->setPageSize(isset($config['pagesize'])?$config['pagesize']:5)
        ->setCurPage(isset($config['curpage'])?$config['curpage']:1);
        $collection->getSelect()->order('ves_review DESC');
        return $collection;
    }

    /**
     * Speical product collection
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection|Object|\Magento\Framework\Data\Collection
     */
    public function getSpecialProducts($config = array())
    {
        /** @var $collection \Magento\Catalog\Model\ResourceModel\Product\Collection */
        $collection = $this->_productCollectionFactory->create()->addAttributeToSelect('*');
        if(is_array($config['cats']) && !empty($config['cats'])){
            $collection->addFieldToFilter('visibility', array(
               \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH,
               \Magento\Catalog\Model\Product\Visibility::VISIBILITY_IN_CATALOG
               ))
            ->addMinimalPrice()
            ->addUrlRewrite()
            ->addTaxPercents()
            ->addStoreFilter()
            ->addFinalPrice();
            $collection ->joinTable(
                'catalog_category_product',
                'product_id=entity_id', 
                array('category_id'=>'category_id'), 
                null, 
                'left')
            ->addAttributeToFilter( array( array('attribute' => 'category_id', 'in' => array('finset' => $config['cats']))))
            ->groupByAttribute('entity_id');
        }
        $collection->addStoreFilter()
        ->addMinimalPrice()
        ->addUrlRewrite()
        ->addTaxPercents()
        ->addFinalPrice();
        $collection->setPageSize(isset($config['pagesize'])?$config['pagesize']:5)
        ->setCurPage(isset($config['curpage'])?$config['curpage']:1);
        $collection->getSelect()->where('price_index.final_price < price_index.price');
        return $collection;
    }

    /**
     * Most viewed product collection
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection|Object|\Magento\Framework\Data\Collection
     */
    public function getMostViewedProducts($config = array())
    {
    	/** @var $collection \Magento\Reports\Model\ResourceModel\Product\CollectionFactory */
        $collection = $this->_reportCollection->create()->addAttributeToSelect('*');
        if(is_array($config['cats']) && !empty($config['cats'])){
            $collection->addFieldToFilter('visibility', array(
               \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH,
               \Magento\Catalog\Model\Product\Visibility::VISIBILITY_IN_CATALOG
               ))
            ->addMinimalPrice()
            ->addUrlRewrite()
            ->addTaxPercents()
            ->addStoreFilter()
            ->addFinalPrice();
            $collection ->joinTable(
                'catalog_category_product',
                'product_id=entity_id', 
                array('category_id'=>'category_id'), 
                null, 
                'left')
            ->addAttributeToFilter( array( array('attribute' => 'category_id', 'in' => array('finset' => $config['cats']))))
            ->groupByAttribute('entity_id');
        }
        $collection->addStoreFilter()
        ->setPageSize(isset($config['pagesize'])?$config['pagesize']:5)
        ->setCurPage(isset($config['curpage'])?$config['curpage']:1);
        return $collection;
    }

    /**
     * Featured product collection
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection|Object|\Magento\Framework\Data\Collection
     */
    public function getFeaturedProducts($config = array())
    {
        /** @var $collection \Magento\Catalog\Model\ResourceModel\Product\Collection */
        $collection = $this->_productCollectionFactory->create()->addAttributeToSelect('*');
        if(is_array($config['cats']) && !empty($config['cats'])){
            $collection->addFieldToFilter('visibility', array(
               \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH,
               \Magento\Catalog\Model\Product\Visibility::VISIBILITY_IN_CATALOG
               ))
            ->addMinimalPrice()
            ->addUrlRewrite()
            ->addTaxPercents()
            ->addStoreFilter()
            ->addFinalPrice();
            $collection ->joinTable(
                'catalog_category_product',
                'product_id=entity_id', 
                array('category_id'=>'category_id'), 
                null, 
                'left')
            ->addAttributeToFilter( array( array('attribute' => 'category_id', 'in' => array('finset' => $config['cats']))))
            ->groupByAttribute('entity_id');
        }
        $collection->addAttributeToFilter(array(array( 'attribute'=>'featured', 'eq' => '1')))
        ->addStoreFilter()
        ->setPageSize(isset($config['pagesize'])?$config['pagesize']:5)
        ->setCurPage(isset($config['curpage'])?$config['curpage']:1);
        return $collection;
    }

    public function getProductBySource($source_key, $config = [])
    {
        $collection = '';
        switch ($source_key) {
            case 'latest':
            $collection = $this->getLatestProducts($config);
            break;
            case 'new_arrivals':
            $collection = $this->getNewarrivalProducts($config);
            break;
            case 'special':
            $collection = $this->getSpecialProducts($config);
            break;
            case 'most_popular':
            $collection = $this->getMostViewedProducts($config);
            break;
            case 'best_seller':
            $collection = $this->getBestsellerProducts($config);
            break;
            case 'top_rated':
            $collection = $this->getTopratedProducts($config);
            break;
            case 'random':
            $collection = $this->getRandomProducts($config);
            break;
            case 'featured':
            $collection = $this->getFeaturedProducts($config);
            break;
        }
        return $collection;
    }
}