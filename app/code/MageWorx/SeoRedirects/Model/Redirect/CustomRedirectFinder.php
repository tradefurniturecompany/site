<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoRedirects\Model\Redirect;

use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use MageWorx\SeoAll\Helper\Page as HelperPage;
use MageWorx\SeoRedirects\Helper\CustomRedirect\Data as HelperData;
use MageWorx\SeoRedirects\Api\Data\CustomRedirectInterface;
use Magento\UrlRewrite\Model\UrlFinderInterface;
use MageWorx\SeoRedirects\Model\ResourceModel\Redirect\CustomRedirect\CollectionFactory as RedirectCollectionFactory;
use MageWorx\SeoRedirects\Model\Redirect\Source\CustomRedirect\RedirectTypeRewriteFragment as RedirectTypeRewriteFragmentSource;

class CustomRedirectFinder
{
    /**
     * @var Url
     */
    protected $urlModel;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * @var RedirectCollectionFactory
     */
    protected $redirectCollectionFactory;

    /** @var UrlFinderInterface */
    protected $urlFinder;

    /** @var RedirectTypeRewriteFragmentSource */
    protected $redirectTypeRewriteFragmentSource;

    /** @var HelperPage */
    protected $helperPage;

    /**
     * CustomRedirectFinder constructor.
     *
     * @param RedirectCollectionFactory $redirectCollectionFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param UrlFinderInterface $urlFinder
     * @param HelperData $helperData
     * @param HelperPage $helperPage
     * @param RedirectTypeRewriteFragmentSource $redirectTypeRewriteFragmentSource
     */
    public function __construct(
        RedirectCollectionFactory $redirectCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        UrlFinderInterface $urlFinder,
        HelperData $helperData,
        HelperPage $helperPage,
        RedirectTypeRewriteFragmentSource $redirectTypeRewriteFragmentSource
    ) {
        $this->redirectCollectionFactory         = $redirectCollectionFactory;
        $this->helperData                        = $helperData;
        $this->helperPage                        = $helperPage;
        $this->urlFinder                         = $urlFinder;
        $this->storeManager                      = $storeManager;
        $this->redirectTypeRewriteFragmentSource = $redirectTypeRewriteFragmentSource;
    }

    /**
     * @param $request
     * @param int $storeId
     * @param null $requestRewrite
     */
    public function getRedirectInfo($request, $storeId, $requestRewrite = null)
    {
        $redirectTypeRewriteFragmentSource = $this->redirectTypeRewriteFragmentSource->toArray();

        $conditions = [];


        if ($requestRewrite) {
            foreach ($redirectTypeRewriteFragmentSource as $redirectType => $fragment) {
                if (strpos($requestRewrite->getTargetPath(), $fragment) !== false) {
                    $conditions[] = [
                        CustomRedirectInterface::REQUEST_ENTITY_TYPE       => $redirectType,
                        CustomRedirectInterface::REQUEST_ENTITY_IDENTIFIER => (int)str_replace(
                            $fragment,
                            '',
                            $requestRewrite->getTargetPath()
                        )
                    ];
                }
            }
        }

        $conditions[] = [
            CustomRedirectInterface::REQUEST_ENTITY_TYPE       => CustomRedirect::REDIRECT_TYPE_CUSTOM,
            CustomRedirectInterface::REQUEST_ENTITY_IDENTIFIER => ltrim($request->getPathInfo(), '/')
            //  /customer-service/  or /customer-service
        ];


        /** @var \MageWorx\SeoRedirects\Model\ResourceModel\Redirect\CustomRedirect\Collection $redirectCollection */
        $redirectCollection = $this->redirectCollectionFactory->create();

        $redirectCollection
            ->addStoreFilter($storeId)
            ->addFieldToFilter(CustomRedirectInterface::STATUS, CustomRedirect::STATUS_ENABLED)
            ->addFrontendFilter($conditions);

        if (count($conditions) > 1) {
            $redirectCollection->addOrder(
                CustomRedirectInterface::REQUEST_ENTITY_TYPE,
                $redirectCollection::SORT_ORDER_ASC
            );
        }

        /** @var \MageWorx\SeoRedirects\Model\Redirect\CustomRedirect $redirect */
        $redirect = $redirectCollection->getFirstItem();

        if (!$redirect->getId()) {
            return null;
        }

        if (!empty($redirectTypeRewriteFragmentSource[$redirect->getTargetEntityType()])) {
            $targetPath    = $redirectTypeRewriteFragmentSource[$redirect->getTargetEntityType(
                )] . $redirect->getTargetEntityIdentifier();
            $targetRewrite = $this->getRewriteByTargetPath($targetPath, $redirect->getStoreId());

            if (!$targetRewrite) {
                return null;
            }

            // Add trailing slash for CMS Page URLs - magento way
            if ($targetRewrite->getEntityType() == 'cms-page') {

                if ($this->helperPage->getIsHomePage(
                    $targetRewrite->getRequestPath(),
                    $targetRewrite->getEntityId(),
                    $storeId
                )) {
                    $url = '';
                } else {
                    $url = $this->helperData->addTrailingSlash($targetRewrite->getRequestPath());
                }
            } else {
                $url = $targetRewrite->getRequestPath();
            }
        } else {
            $url = $redirect->getTargetEntityIdentifier();
        }

        /**
         * Wrap object
         */
        $data = [];

        $data['code']               = $redirect->getRedirectCode();
        $data['url']                = $url;
        $data['is_custom_redirect'] = $redirect->getTargetEntityType() == CustomRedirect::REDIRECT_TYPE_CUSTOM;

        return $data;
    }

    /**
     * @param string $requestPath
     * @param int $storeId
     * @return UrlRewrite|null
     */
    protected function getRewriteByTargetPath($targetPath, $storeId)
    {
        return $this->urlFinder->findOneByData(
            [
                UrlRewrite::TARGET_PATH => trim($targetPath, '/'),
                UrlRewrite::STORE_ID    => $storeId,
            ]
        );
    }
}
