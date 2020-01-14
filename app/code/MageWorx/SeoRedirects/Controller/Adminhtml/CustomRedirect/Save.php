<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoRedirects\Controller\Adminhtml\CustomRedirect;

use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\ValidatorException;
use MageWorx\SeoRedirects\Controller\Adminhtml\CustomRedirect as CustomRedirectController;
use MageWorx\SeoRedirects\Controller\RegistryConstants;
use Magento\Framework\Exception\LocalizedException;
use MageWorx\SeoRedirects\Model\Redirect\Source\CustomRedirect\RedirectTypeRequestKey;
use MageWorx\SeoRedirects\Model\Redirect\Source\CustomRedirect\RedirectTypeTargetKey;
use MageWorx\SeoRedirects\Model\Redirect\Source\RedirectTypeEntity;
use Magento\UrlRewrite\Model\UrlFinderInterface;
use MageWorx\SeoRedirects\Api\Data\CustomRedirectInterface;
use MageWorx\SeoRedirects\Model\Redirect\CustomRedirect;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;

class Save extends CustomRedirectController
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var \MageWorx\SeoAll\Model\ResourceModel\Category
     */
    protected $resourceCategory;

    /**
     * @var \Magento\Cms\Model\PageFactory
     */
    protected $pageFactory;

    /**
     * @var \MageWorx\SeoRedirects\Model\ResourceModel\Redirect\CustomRedirect\CollectionFactory
     */
    protected $redirectCollectionFactory;

    /**
     * URL builder
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var UrlFinderInterface
     */
    protected $urlFinder;

    /**
     * @var array
     */
    protected $productStores = [];

    /**
     * @var array
     */
    protected $categoryStores = [];

    /**
     * @var array
     */
    protected $pageStores = [];


    public function __construct(
        \MageWorx\SeoRedirects\Controller\Adminhtml\CustomRedirectHelper $customRedirectHelper,
        \MageWorx\SeoRedirects\Model\Redirect\CustomRedirectRepository $customRedirectRepository,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        RedirectTypeRequestKey $redirectTypeRequestKeyOptions,
        RedirectTypeTargetKey $redirectTypeTargetKeyOptions,
        RedirectTypeEntity $redirectTypeEntityOptions,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \MageWorx\SeoAll\Model\ResourceModel\Category $resourceCategory,
        \Magento\Cms\Model\PageFactory $pageFactory,
        \MageWorx\SeoRedirects\Model\ResourceModel\Redirect\CustomRedirect\CollectionFactory $redirectCollectionFactory,
        UrlFinderInterface $urlFinder,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->customRedirectHelper          = $customRedirectHelper;
        $this->resultPageFactory             = $resultPageFactory;
        $this->redirectTypeRequestKeyOptions = $redirectTypeRequestKeyOptions;
        $this->redirectTypeTargetKeyOptions  = $redirectTypeTargetKeyOptions;
        $this->redirectTypeEntityOptions     = $redirectTypeEntityOptions;
        $this->storeManager                  = $storeManager;
        $this->productFactory                = $productFactory;
        $this->categoryFactory               = $categoryFactory;
        $this->redirectCollectionFactory     = $redirectCollectionFactory;
        $this->resourceCategory              = $resourceCategory;
        $this->pageFactory                   = $pageFactory;
        $this->urlFinder                     = $urlFinder;
        parent::__construct($registry, $customRedirectRepository, $context);
    }

    /**
     * Run the action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $data           = $this->getRequest()->getPost(RegistryConstants::CUSTOM_REDIRECT_FORM_DATA_KEY);
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($data) {
            $data       = $this->prepareData($data);
            $redirectId = !empty($data['redirect_id']) ? $data['redirect_id'] : null;

            if ($this->isRecursiveRedirect($data)) {
                $message = __("Request and target entities(URLs) can't be identical!");
                $this->messageManager->addErrorMessage($message);
                $resultRedirect->setPath('mageworx_seoredirects/*/');

                return $resultRedirect;
            }

            $isEdit = (bool)$redirectId;

            /** @var \MageWorx\SeoRedirects\Api\Data\CustomRedirectInterface $redirect */
            $redirect = $this->customRedirectHelper->initRedirect($redirectId);
            $redirect->setData($data);

            try {
                $storeIds = $this->getRedirectStores($data);

                foreach ($storeIds as $storeId) {
                    $successFlag = true;
                    $storeName   = $this->storeManager->getStore($storeId)->getName();

                    if (!$this->validateEntitiesByStore($data, $storeId)) {
                        $message = __(
                            'It is impossible to save redirect for %storeName Store',
                            ['storeName' => $storeName]
                        );
                        $this->messageManager->addNoticeMessage($message);
                        continue;
                    }

                    //For new redirect - several store_id are possible.
                    if (!$isEdit) {
                        $redirect->setRedirectId(null);
                    }

                    $redirect->setStoreId($storeId);

                    $this->_eventManager->dispatch(
                        'mageworx_seoredirects_customredirect_prepare_save',
                        [
                            'redirect' => $redirect,
                            'request'  => $this->getRequest()
                        ]
                    );

                    try {
//                        $this->urlFinder->findAllByData()

                        $this->addRedirectPriorityMessages($redirect, $storeName);

                        $redirect->save();
                    } catch (LocalizedException $e) {
                        $successFlag = false;
                        $this->messageManager->addErrorMessage($e->getMessage());
                    }

                    if ($successFlag) {
                        $successMessage = __(
                            'The redirect for %storeName Store has been saved.',
                            ['storeName' => $storeName]
                        );
                        $this->messageManager->addSuccessMessage($successMessage);
                    }

                    if ($isEdit) {
                        break;
                    }
                }
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (InputException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('Something went wrong while saving the redirect.')
                );
            }

            if ($isEdit && $this->getRequest()->getParam('back')) {
                $resultRedirect->setPath(
                    'mageworx_seoredirects/*/edit',
                    [
                        'id'       => $redirect->getId(),
                        '_current' => true
                    ]
                );

                return $resultRedirect;
            }
        }

        $resultRedirect->setPath('mageworx_seoredirects/*/');

        return $resultRedirect;
    }

    /**
     * @param \MageWorx\SeoRedirects\Api\Data\CustomRedirectInterface $newRedirect
     * @return void
     */
    public function addRedirectPriorityMessages($newRedirect, $storeName)
    {
        if ($newRedirect->getRequestEntityType() == CustomRedirect::REDIRECT_TYPE_CUSTOM) {
            $currentUrlRewrites = $this->urlFinder->findAllByData(
                [
                    UrlRewrite::STORE_ID     => $newRedirect->getStoreId(),
                    UrlRewrite::REQUEST_PATH => $newRedirect->getRequestEntityIdentifier(),
                ]
            );

            $conditions = [];

            foreach ($currentUrlRewrites as $rewrite) {
                switch ($rewrite->getEntityType()) {
                    case 'category':
                        $conditions[] = [
                            CustomRedirectInterface::REQUEST_ENTITY_TYPE       => CustomRedirect::REDIRECT_TYPE_CATEGORY,
                            CustomRedirectInterface::REQUEST_ENTITY_IDENTIFIER => $rewrite->getEntityId()
                        ];
                        break;
                    case 'product':
                        $conditions[] = [
                            CustomRedirectInterface::REQUEST_ENTITY_TYPE       => CustomRedirect::REDIRECT_TYPE_PRODUCT,
                            CustomRedirectInterface::REQUEST_ENTITY_IDENTIFIER => $rewrite->getEntityId()
                        ];
                        break;
                    case 'cms-page':
                        $conditions[] = [
                            CustomRedirectInterface::REQUEST_ENTITY_TYPE       => CustomRedirect::REDIRECT_TYPE_PAGE,
                            CustomRedirectInterface::REQUEST_ENTITY_IDENTIFIER => $rewrite->getEntityId()
                        ];
                        break;
                }

                if ($conditions) {
                    /** @var \MageWorx\SeoRedirects\Model\ResourceModel\Redirect\CustomRedirect\Collection $redirectCollection */
                    $redirectCollection = $this->redirectCollectionFactory->create();
                    $redirectCollection->addStoreFilter($newRedirect->getStoreId());
                    $redirectCollection->addFrontendFilter($conditions);
                }
            }

            if (!empty($redirectCollection)) {
                /** @var \MageWorx\SeoRedirects\Api\Data\CustomRedirectInterface $redirect */
                foreach ($redirectCollection as $redirect) {
                    switch ($redirect->getRequestEntityType()) {
                        case CustomRedirect::REDIRECT_TYPE_CATEGORY:
                            $entityType = __('category');
                            break;
                        case CustomRedirect::REDIRECT_TYPE_PRODUCT:
                            $entityType = __('product');
                            break;
                        case CustomRedirect::REDIRECT_TYPE_PAGE:
                            $entityType = __('CMS page');
                            break;
                        case CustomRedirect::REDIRECT_TYPE_LANDINGPAGE:
                            $entityType = __('Landing page');
                            break;
                    }

                    $message = __(
                        'Note. For "%storeName" Store the input Request Path (%requestPath) is identical to url key of product (ID#%productId) and more priority than it',
                        [
                            'storeName'   => $storeName,
                            'requestPath' => $newRedirect->getRequestEntityIdentifier(),
                            'entityType'  => $entityType,
                            'entityId'    => $redirect->getRequestEntityIdentifier()
                        ]
                    );

                    $this->messageManager->addNoticeMessage($message);
                }
            }
        }
    }

    /**
     * @param array $data
     * @return array
     */
    protected function prepareData($data)
    {
        $redirectTypeEntityOptions = $this->redirectTypeEntityOptions->toArray();
        $requestKeyOptions         = $this->redirectTypeRequestKeyOptions->toArray();
        $requestKey                = $requestKeyOptions[$data[CustomRedirectInterface::REQUEST_ENTITY_TYPE]];

        if (!empty($data[$requestKey])) {
            if ($data[CustomRedirectInterface::REQUEST_ENTITY_TYPE] != CustomRedirect::REDIRECT_TYPE_CUSTOM) {
                $result = str_replace(['category/', 'product/', 'page/'], '', $data[$requestKey]);
            } else {
                $result = $data[$requestKey];
            }
            $data[CustomRedirectInterface::REQUEST_ENTITY_IDENTIFIER] = $result;
        } else {
            InputException::requiredField(
                $redirectTypeEntityOptions[$data[CustomRedirectInterface::REQUEST_ENTITY_TYPE]]
            );
        }

        $targetKeyOptions = $this->redirectTypeTargetKeyOptions->toArray();
        $targetKey        = $targetKeyOptions[$data[CustomRedirectInterface::TARGET_ENTITY_TYPE]];

        if (!empty($data[$targetKey])) {
            if ($data[CustomRedirectInterface::TARGET_ENTITY_TYPE] != CustomRedirect::REDIRECT_TYPE_CUSTOM) {
                $result = str_replace(['category/', 'product/', 'page/'], '', $data[$targetKey]);
            } else {
                $result = $data[$targetKey];
            }
            $data[CustomRedirectInterface::TARGET_ENTITY_IDENTIFIER] = $result;
        } else {
            InputException::requiredField(
                $redirectTypeEntityOptions[$data[CustomRedirectInterface::TARGET_ENTITY_TYPE]]
            );
        }

        return $data;
    }

    /**
     * @param array $data
     * @return array
     */
    protected function getRedirectStores(array $data)
    {
        $storeIds = [];

        if (!empty($data['stores'])) {
            $stores = $data['stores'];
            if (in_array('0', $stores)) {
                foreach ($this->storeManager->getStores() as $store) {
                    $storeIds[] = $store->getId();
                }
            } else {
                $storeIds = $stores;
            }
        } else {
            $storeIds[] = $data['store_id'];
        }

        return $storeIds;
    }

    /**
     * @param array $data
     * @param int $storeId
     * @return bool
     */
    protected function validateEntitiesByStore(array $data, $storeId)
    {
        switch ($data[CustomRedirectInterface::REQUEST_ENTITY_TYPE]) {
            case CustomRedirect::REDIRECT_TYPE_CATEGORY:
                $categoryId = $data[CustomRedirectInterface::REQUEST_ENTITY_IDENTIFIER];
                if (!$this->validateCategoryByStore($categoryId, $storeId)) {
                    return false;
                }
                break;
            case CustomRedirect::REDIRECT_TYPE_PRODUCT:
                $productId = $data[CustomRedirectInterface::REQUEST_ENTITY_IDENTIFIER];
                if (!$this->validateProductByStore($productId, $storeId)) {
                    return false;
                }
                break;
            case CustomRedirect::REDIRECT_TYPE_PAGE:
                $pageId = $data[CustomRedirectInterface::REQUEST_ENTITY_IDENTIFIER];
                if (!$this->validatePageByStore($pageId, $storeId)) {
                    return false;
                }
                break;
        }

        switch ($data[CustomRedirectInterface::TARGET_ENTITY_TYPE]) {
            case CustomRedirect::REDIRECT_TYPE_CATEGORY:
                $categoryId = $data[CustomRedirectInterface::TARGET_ENTITY_IDENTIFIER];
                if (!$this->validateCategoryByStore($categoryId, $storeId)) {
                    return false;
                }
                break;
            case CustomRedirect::REDIRECT_TYPE_PRODUCT:
                $productId = $data[CustomRedirectInterface::TARGET_ENTITY_IDENTIFIER];

                if (!$this->validateProductByStore($productId, $storeId)) {
                    return false;
                }
                break;
            case CustomRedirect::REDIRECT_TYPE_PAGE:
                $pageId = $data[CustomRedirectInterface::TARGET_ENTITY_IDENTIFIER];
                if (!$this->validatePageByStore($pageId, $storeId)) {
                    return false;
                }
                break;
        }

        return true;
    }

    /**
     * @param int $categoryId
     * @param int $storeId
     * @return bool
     */
    protected function validateCategoryByStore($categoryId, $storeId)
    {
        if (empty($this->categoryStores[$categoryId])) {
            /** @var \Magento\Catalog\Model\Category $category */
            $category = $this->categoryFactory->create();

            /**
             * \Magento\Catalog\Model\Category::KEY_PATH (path) normally has global scope
             */
            $categoryData = $this->resourceCategory->getAttributeRawValue(
                $categoryId,
                \Magento\Catalog\Model\Category::KEY_PATH,
                \Magento\Store\Model\Store::DEFAULT_STORE_ID
            );

            if (is_array($categoryData) && array_key_exists(\Magento\Catalog\Model\Category::KEY_PATH, $categoryData)) {
                $categoryPath = $categoryData[\Magento\Catalog\Model\Category::KEY_PATH];
            } else {
                $categoryPath = $categoryData;
            }

            $category->setId($categoryId);
            $category->setPath($categoryPath);
            $categoryStores = $category->getStoreIds();

            /**
             * @todo Check for single store
             */
            if ($categoryStores[0] === 0) {
                array_shift($categoryStores);
            }

            $this->categoryStores[$categoryId] = $categoryStores;
        }

        return in_array($storeId, $this->categoryStores[$categoryId]);
    }

    /**
     * @param int $productId
     * @param int $storeId
     * @return bool
     */
    protected function validateProductByStore($productId, $storeId)
    {
        if (empty($this->productStores[$productId])) {
            /** @var \Magento\Catalog\Model\Product $product */
            $product       = $this->productFactory->create();
            $productStores = $product->setId($productId)->getStoreIds();

            $this->productStores[$productId] = $productStores;
        }

        return in_array($storeId, $this->productStores[$productId]);
    }

    /**
     * @param int $pageId
     * @param int $storeId
     * @return bool
     */
    protected function validatePageByStore($pageId, $storeId)
    {
        if (empty($this->pageStores[$pageId])) {
            /** @var \Magento\Cms\Model\Page $page */
            $page                      = $this->pageFactory->create()->load($pageId);
            $pageStores                = $page->getStores();
            $this->pageStores[$pageId] = $pageStores;
        }

        if (in_array('0', $this->pageStores[$pageId])) {
            return true;
        }

        return in_array($storeId, $this->pageStores[$pageId]);
    }

    /**
     * @param $data
     * @return bool
     */
    protected function isRecursiveRedirect($data)
    {
        return $data[CustomRedirectInterface::REQUEST_ENTITY_TYPE] == $data[CustomRedirectInterface::REQUEST_ENTITY_TYPE]
            && $data['request_entity_identifier'] == $data['target_entity_identifier'];
    }
}
