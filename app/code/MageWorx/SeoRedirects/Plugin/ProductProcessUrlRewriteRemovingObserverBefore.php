<?php
/**
 * Copyright © 2015 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoRedirects\Plugin;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\UrlRewrite\Model\UrlFinderInterface;
use Magento\CatalogUrlRewrite\Model\ProductUrlRewriteGenerator;
use Magento\UrlRewrite\Service\V1\Data\UrlRewriteFactory;
use MageWorx\SeoRedirects\Model\Redirect\DpRedirectFactory;
use MageWorx\SeoRedirects\Model\Redirect\DpRedirect;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Category\Collection as CategoryCollection;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\ResourceModel\Product as ResourceProduct;
use Magento\Framework\Stdlib\DateTime\DateTime;

class ProductProcessUrlRewriteRemovingObserverBefore
{

    protected $_priorityCategories = [];

    protected $_productNames = [];

    /**
     * @var UrlFinderInterface
     */
    protected $urlFinder;

    /**
     * @var DpRedirectFactory
     */
    protected $dpRedirectFactory;

    /**
     * @var DpRedirect
     */
    protected $dpRedirect;

    /**
     * @var CategoryCollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * @var CategoryCollection
     */
    protected $categoryCollection;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product
     */
    protected $resourceProduct;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $dateModel;

    /**
     * ProductProcessUrlRewriteRemovingObserverBefore constructor.
     *
     * @param \Magento\UrlRewrite\Model\UrlFinderInterface $urlFinder
     * @param DpRedirectFactory $dpRedirectFactory
     * @param CategoryCollectionFactory $categoryCollectionFactory
     * @param StoreManagerInterface $storeManager
     * @param ResourceProduct $resourceProduct
     * @param DateTime $dateModel
     */
    public function __construct(
        \Magento\UrlRewrite\Model\UrlFinderInterface $urlFinder,
        DpRedirectFactory $dpRedirectFactory,
        CategoryCollectionFactory $categoryCollectionFactory,
        StoreManagerInterface $storeManager,
        ResourceProduct $resourceProduct,
        DateTime $dateModel
    ) {
        $this->urlFinder                 = $urlFinder;
        $this->dpRedirectFactory         = $dpRedirectFactory;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->storeManager              = $storeManager;
        $this->resourceProduct           = $resourceProduct;
        $this->dateModel                 = $dateModel;
    }

    /**
     * @param \Magento\CatalogUrlRewrite\Observer\ProductProcessUrlRewriteRemovingObserver $subject
     * @param \Magento\Framework\Event\Observer $observer
     * @return array|void
     * @throws \Exception
     */
    public function beforeExecute($subject, $observer)
    {
        /**@var $redirect \MageWorx\SeoRedirects\Model\Redirect\DpRedirect * */
        $redirect = $this->dpRedirectFactory->create();

        $product = $observer->getEvent()->getProduct();

        if (!$product) {
            return [$observer];
        }

        //duplicated and non-saved product
        if (!$product->getSku()) {
            return [$observer];
        }

        $currentUrlRewrites = $this->urlFinder->findAllByData(
            [
                UrlRewrite::ENTITY_ID   => $product->getId(),
                UrlRewrite::ENTITY_TYPE => ProductUrlRewriteGenerator::ENTITY_TYPE,
            ]
        );

        foreach ($currentUrlRewrites as $rewrite) {
            $priorityCategoryId = $this->getPriorityCategoryByProduct($product, $rewrite->getStoreId());

            if (!$priorityCategoryId) {
                continue;
            }

            /**@var \MageWorx\SeoRedirects\Model\Redirect\DpRedirect * */
            $redirect = $this->dpRedirectFactory->create();

            $redirect->setRequestPath($rewrite->getRequestPath());
            $redirect->setStoreId($rewrite->getStoreId());
            $redirect->setProductId($product->getId());

            $categoryId       = null;
            $requestPathParts = explode('/', $rewrite->getTargetPath());
            $categoryKey      = array_keys($requestPathParts, 'category');

            if (!empty($categoryKey[0]) && !empty($requestPathParts[$categoryKey[0] + 1]) && is_numeric(
                    $requestPathParts[$categoryKey[0] + 1]
                )) {
                $categoryId = $requestPathParts[$categoryKey[0] + 1];
            }

            if (empty($categoryId)) {
                $redirect->setCategoryId($priorityCategoryId);
            } else {
                $redirect->setCategoryId($categoryId);
            }
            $redirect->setPriorityCategoryId($priorityCategoryId);
            $redirect->setProductId($product->getId());
            $redirect->setProductSku($product->getSku());
            $redirect->setProductName($this->_getProductName($product->getId(), $rewrite->getStoreId()));
            $redirect->setDateCreated($this->dateModel->gmtDate());

            $redirect->isObjectNew(true);

            $redirect->getResource()->save($redirect);

            if (strpos($rewrite->getTargetPath(), 'catalog/product/view/') === 0) {
                $redirect->setRedirectId(null);
                $redirect->setRequestPath($rewrite->getTargetPath());
                $redirect->isObjectNew(true);
                $redirect->getResource()->save($redirect);
            }
        }

        return [$observer];
    }

    /**
     *
     * @param Mage_Catalog_Model_Product $product
     * @return int
     */
    protected function getPriorityCategoryByProduct($product, $storeId)
    {
        if (!isset($this->_priorityCategories[$storeId])) {
            $categoryIds = $product->getCategoryIds();

            if (!$categoryIds) {
                $this->_priorityCategories[$storeId] = false;

                return $this->_priorityCategories[$storeId];
            }

            $categoryPriority = [];

            $rootCategoryId = $this->storeManager->getStore($storeId)->getRootCategoryId();

            $categoryCollection = $this->categoryCollectionFactory->create();
            $categoryCollection->addAttributeToFilter('entity_id', $categoryIds);
            $categoryCollection->addFieldToFilter('is_active', ['eq' => 1]);
            $categoryCollection->addAttributeToFilter('path', ['like' => "1/{$rootCategoryId}/%"]);
            $categoryCollection->addAttributeToSelect('redirect_priority');
            $categoryCollection->setStoreId($storeId);

            foreach ($categoryCollection->getItems() as $category) {
                $redirectPriority                     = empty($category['redirect_priority']) ? 0 : (int)$category['redirect_priority'];
                $categoryPriority[$category->getId()] = $redirectPriority;
            }

            reset($categoryPriority);
            arsort($categoryPriority);

            if ($categoryPriority) {
                $this->_priorityCategories[$storeId] = key($categoryPriority);
            } else {
                $this->_priorityCategories[$storeId] = false;
            }
        }

        return $this->_priorityCategories[$storeId];
    }

    /**
     *
     * @param int $productId
     * @param int $storeId
     * @return string
     */
    protected function _getProductName($productId, $storeId)
    {
        if (!array_key_exists($storeId, $this->_productNames)) {
            $this->_productNames[$storeId] = $this->resourceProduct->getAttributeRawValue(
                $productId,
                'name',
                $this->storeManager->getStore($storeId)
            );
        }

        return $this->_productNames[$storeId];
    }
}
