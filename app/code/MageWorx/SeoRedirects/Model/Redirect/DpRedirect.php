<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoRedirects\Model\Redirect;

use MageWorx\SeoRedirects\Model\Redirect;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Filter\FilterManager;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;
use MageWorx\SeoRedirects\Helper\DpRedirect\Data as HelperData;
use MageWorx\SeoRedirects\Api\Data\DpRedirectInterface;

class DpRedirect extends \MageWorx\SeoRedirects\Model\Redirect implements DpRedirectInterface
{

    const TARGET_SELF_CATEGORY = 0;

    const TARGET_PRIORITY_CATEGORY = 1;

    /**
     * @var Url
     */
    protected $urlModel;
    /**
     * Cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'mageworx_seoredirects_dpredirect';

    /**
     * cache tag
     *
     * @var string
     */
    protected $cacheTag = 'mageworx_seoredirects_dpredirect';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'mageworx_seoredirects_dpredirect';

    /**
     * filter model
     *
     * @var \Magento\Framework\Filter\FilterManager
     */
    protected $filter;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * DpRedirect constructor.
     *
     * @param FilterManager $filter
     * @param Context $context
     * @param Registry $registry
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param HelperData $helperData
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
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->filter       = $filter;
        $this->helperData   = $helperData;
        $this->storeManager = $storeManager;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('MageWorx\SeoRedirects\Model\ResourceModel\Redirect\DpRedirect');
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
     * Get Redirect ID
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->getData(DpRedirectInterface::REDIRECT_ID);
    }

    /**
     * Get Deleted Product ID
     *
     * @return int|null
     */
    public function getProductId()
    {
        return $this->getData(DpRedirectInterface::PRODUCT_ID);
    }

    /**
     * Get Deleted Product Name
     *
     * @return string
     */
    public function getProductName()
    {
        return $this->getData(DpRedirectInterface::PRODUCT_NAME);
    }

    /**
     * Get Deleted Product SKU
     *
     * @return string
     */
    public function getProductSku()
    {
        return $this->getData(DpRedirectInterface::PRODUCT_SKU);
    }

    /**
     * Get Redirect Store ID
     *
     * @return int
     */
    public function getStoreId()
    {
        return $this->getData(DpRedirectInterface::STORE_ID);
    }

    /**
     * Get Request Path
     *
     * @return string
     */
    public function getRequestPath()
    {
        return $this->getData(DpRedirectInterface::REQUEST_PATH);
    }

    /**
     * Get Category ID
     *
     * @return int
     */
    public function getCategoryId()
    {
        return $this->getData(DpRedirectInterface::CATEGORY_ID);
    }

    /**
     * Get Priority Category ID
     *
     * @return int
     */
    public function getPriorityCategoryId()
    {
        return $this->getData(DpRedirectInterface::PRIORITY_CATEGORY_ID);
    }

    /**
     * Get Redirect Data Created
     *
     * @return string
     */
    public function getDateCreated()
    {
        return $this->getData(DpRedirectInterface::DATE_CREATED);
    }

    /**
     * Get Redirect Hits
     *
     * @return int
     */
    public function getHits()
    {
        return $this->getData(DpRedirectInterface::HITS);
    }

    /**
     * Get Status
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->getData(DpRedirectInterface::STATUS);
    }

    /**
     * Set Redirect ID
     *
     * @param int $id
     * @return DpRedirectInterface
     */
    public function setId($id)
    {
        return $this->setData(DpRedirectInterface::REDIRECT_ID, $id);
    }

    /**
     * Set Product ID
     *
     * @param int $productId
     * @return DpRedirectInterface
     */
    public function setProductId($productId)
    {
        return $this->setData(DpRedirectInterface::PRODUCT_ID, $productId);
    }

    /**
     * Set Product Name
     *
     * @param string $productName
     * @return DpRedirectInterface
     */
    public function setProductName($productName)
    {
        return $this->setData(DpRedirectInterface::PRODUCT_NAME, $productName);
    }

    /**
     * Set Product SKU
     *
     * @param string $productSku
     * @return DpRedirectInterface
     */
    public function setProductSku($productSku)
    {
        return $this->setData(DpRedirectInterface::PRODUCT_SKU, $productSku);
    }

    /**
     * Set Redirect Store ID
     *
     * @param int $storeId
     * @return DpRedirectInterface
     */
    public function setStoreId($storeId)
    {
        return $this->setData(DpRedirectInterface::STORE_ID, $storeId);
    }

    /**
     * Set Redirect Request Path
     *
     * @param string $requestPath
     * @return DpRedirectInterface
     */
    public function setRequestPath($requestPath)
    {
        return $this->setData(DpRedirectInterface::REQUEST_PATH, $requestPath);
    }

    /**
     * Set Category ID
     *
     * @param int $categoryId
     * @return DpRedirectInterface
     */
    public function setCategoryId($categoryId)
    {
        return $this->setData(DpRedirectInterface::CATEGORY_ID, $categoryId);
    }

    /**
     * Set Priority Category ID
     *
     * @param int $categoryId
     * @return DpRedirectInterface
     */
    public function setPriorityCategoryId($categoryId)
    {
        return $this->setData(DpRedirectInterface::PRIORITY_CATEGORY_ID, $categoryId);
    }

    /**
     * Set Date Created
     *
     * @param string $dateCreated
     * @return DpRedirectInterface
     */
    public function setDataCreated($dateCreated)
    {
        return $this->setData(DpRedirectInterface::DATE_CREATED, $dateCreated);
    }

    /**
     * Set Redirect Hits
     *
     * @param int $hits
     * @return DpRedirectInterface
     */
    public function setHits($hits)
    {
        return $this->setData(DpRedirectInterface::HITS, $hits);
    }

    /**
     * Set Redirect Status
     *
     * @param int $statusCode
     * @return DpRedirectInterface
     */
    public function setStatus($statusCode)
    {
        return $this->setData(DpRedirectInterface::STATUS, $statusCode);
    }

    /**
     * Get default deleted products redirect values
     *
     * @return array
     */
    public function getDefaultValues()
    {
        return [];
    }
}
