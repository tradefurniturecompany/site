<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoRedirects\Observer;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\UrlRewrite\Model\UrlFinderInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewriteFactory;
use MageWorx\SeoRedirects\Model\ResourceModel\Redirect\DpRedirect\CollectionFactory as DpRedirectCollectionFactory;
use MageWorx\SeoRedirects\Model\Redirect\DpRedirect;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Category\Collection as CategoryCollection;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\ResourceModel\Product as ResourceProduct;
use Magento\Framework\Stdlib\DateTime\DateTime;
use MageWorx\SeoRedirects\Helper\DpRedirect\Data as HelperData;
use MageWorx\SeoRedirects\Helper\StringHelper as HelperString;
use Magento\Framework\App\ResponseFactory;

class DpRedirectMaker implements \Magento\Framework\Event\ObserverInterface
{
    protected $_priorityCategories = [];

    protected $_productNames = [];

    /**
     * @var UrlFinderInterface
     */
    protected $urlFinder;

    /**
     * @var DpRedirectCollectionFactory
     */
    protected $dpRedirectCollectionFactory;

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
     * @var HelperData
     */
    protected $helperData;

    /**
     * HelperString
     */
    protected $helperString;

    /**
     * @var ResponseFactory
     */
    protected $responseFactory;


    /**
     * @var \Zend\Console\Response
     */
    protected $response;

    /**
     * DpRedirectMaker constructor.
     *
     * @param UrlFinderInterface $urlFinder
     * @param DpRedirectCollectionFactory $dpRedirectCollectionFactory
     * @param CategoryCollectionFactory $categoryCollectionFactory
     * @param StoreManagerInterface $storeManager
     * @param ResourceProduct $resourceProduct
     * @param DateTime $dateModel
     * @param HelperData $helperData
     * @param HelperString $helperString
     * @param ResponseFactory $responseFactory
     * @param \Zend\Console\Response $response
     */
    public function __construct(
        \Magento\UrlRewrite\Model\UrlFinderInterface $urlFinder,
        DpRedirectCollectionFactory $dpRedirectCollectionFactory,
        CategoryCollectionFactory $categoryCollectionFactory,
        StoreManagerInterface $storeManager,
        ResourceProduct $resourceProduct,
        DateTime $dateModel,
        HelperData $helperData,
        HelperString $helperString,
        ResponseFactory $responseFactory,
        \Zend\Console\Response $response
    ) {
        $this->urlFinder                   = $urlFinder;
        $this->dpRedirectCollectionFactory = $dpRedirectCollectionFactory;
        $this->categoryCollectionFactory   = $categoryCollectionFactory;
        $this->storeManager                = $storeManager;
        $this->resourceProduct             = $resourceProduct;
        $this->dateModel                   = $dateModel;
        $this->helperData                  = $helperData;
        $this->helperString                = $helperString;
        $this->responseFactory             = $responseFactory;
        $this->response                    = $response;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $request = $observer->getEvent()->getRequest();

        if (!$request) {
            return;
        }

        if (!$this->helperData->isEnabled()) {
            return;
        }

        $storeId   = $this->storeManager->getStore()->getId();
        $storeCode = $this->storeManager->getStore()->getCode();

        list($requestUrlRaw) = explode('?', $request->getRequestUri());

        $requestUrl = $this->helperString->cropFirstPart($requestUrlRaw, ['/', 'index.php/', $storeCode, '/'], true);

        $redirectCollection = $this->dpRedirectCollectionFactory->create();
        $redirectCollection->addRequestPathsFilter([$requestUrl, rtrim($requestUrl, '/') . '/']);
        $redirectCollection->addStoreFilter($storeId);

        /**
         * @var \MageWorx\SeoRedirects\Model\Redirect\Product
         */
        $redirect = $redirectCollection->load()->getFirstItem();

        if (!empty($redirect['category_id'])) {
            $categoryIds = array_unique([$redirect['category_id'], $redirect['priority_category_id']]);

            $collection = $this->categoryCollectionFactory->create();
            $collection->addAttributeToFilter('entity_id', $categoryIds);
            $collection->addFieldToFilter('is_active', ['eq' => 1]);
            $collection->setStoreId($storeId);

            $category = $this->_getCategoryModel($collection, $redirect);

            if ($category) {
                $redirect->setHits($redirect->getHits() + 1);
                $redirect->save();
                $redirectCode = $this->helperData->getRedirectType();

                $this->responseFactory->create()->setRedirect($category->getUrl(), $redirectCode)->sendResponse();

                //We need to avoid using "exit" in own code for corresponding Marketplace requirements
                $this->response->send();
            }
        }

        return;
    }


    /**
     * @param \Magento\Catalog\Model\ResourceModel\Category\Collection\ $collection
     * @param \MageWorx\SeoRedirects\Model\Redirect\DpRedirect $redirect
     * @return \Magento\Catalog\Model\Category
     */
    protected function _getCategoryModel($collection, $redirect)
    {
        if ($collection->count() < 2) {
            return $collection->getFirstItem();
        }

        $isUsePriorityCategory = $this->helperData->isForceProductRedirectByPriority();

        foreach ($collection as $item) {
            if ($isUsePriorityCategory) {
                if ($redirect['priority_category_id'] == $item->getId()) {
                    return $item;
                }
            } else {
                if ($redirect['category_id'] == $item->getId()) {
                    return $item;
                }
            }
        }
    }
}
