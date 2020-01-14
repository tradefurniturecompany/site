<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoUrls\Controller;

use Magento\UrlRewrite\Controller\Adminhtml\Url\Rewrite;

class Router extends \Magento\UrlRewrite\Controller\Router
{
    /**
     * @var \MageWorx\SeoUrls\Helper\Data
     */
    protected $helperData;

    /**
     * @var \MageWorx\SeoUrls\Helper\SeoUrlParser
     */
    protected $seoUrlParser;

    /**
     * Seo Url Builder
     *
     * @var \MageWorx\SeoUrls\Helper\SeoUrlBuilder
     */
    protected $seoUrlBuilder;

    /**
     * Router constructor.
     * @param \MageWorx\SeoUrls\Helper\Data $helperData
     * @param \MageWorx\SeoUrls\Helper\SeoUrlParser $seoUrlParser
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\UrlRewrite\Model\UrlFinderInterface $urlFinder
     * @param \Magento\Framework\App\ActionFactory $actionFactory
     * @param \Magento\Framework\UrlInterface $url
     * @param \Magento\Framework\App\ResponseInterface $response
     */
    public function __construct(
        \MageWorx\SeoUrls\Helper\Data $helperData,
        \MageWorx\SeoUrls\Helper\SeoUrlParser $seoUrlParser,
        \MageWorx\SeoUrls\Helper\SeoUrlBuilder $seoUrlBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\UrlRewrite\Model\UrlFinderInterface $urlFinder,
        \Magento\Framework\App\ActionFactory $actionFactory,
        \Magento\Framework\UrlInterface $url,
        \Magento\Framework\App\ResponseInterface $response
    ) {
        parent::__construct($actionFactory, $url, $storeManager, $response, $urlFinder);
        $this->seoUrlBuilder = $seoUrlBuilder;
        $this->helperData    = $helperData;
        $this->seoUrlParser  = $seoUrlParser;
    }

    public function match(\Magento\Framework\App\RequestInterface $request)
    {
        $urlData = $this->seoUrlParser->getConvertedUrlData($request->getRequestUri(), $request->getPathInfo());

        if (!$this->isParsed($request->getRequestUri(), $urlData)) {
            return false;
        }
        /** @var \Magento\UrlRewrite\Service\V1\Data\UrlRewrite $rewrite */
        $rewrite = $this->getRewrite($urlData['path'], $this->storeManager->getStore()->getId());

        if ($rewrite === null) {
            return null;
        }

        if ($rewrite->getRedirectType()) {
            return $this->processRedirect($request, $rewrite);
        }

        $params = $this->seoUrlParser->rebuildParams($urlData['params']);

        $request->setAlias(\Magento\Framework\UrlInterface::REWRITE_REQUEST_PATH_ALIAS, $rewrite->getRequestPath());
        $request->setPathInfo('/' . $rewrite->getTargetPath());
        $request->setParams($params);

        $isP = isset($urlData['params'][\MageWorx\SeoUrls\Helper\UrlParser\Pager::PARAMS_KEY]);
        $isL = isset($urlData['params'][\MageWorx\SeoUrls\Helper\UrlParser\Layer::PARAMS_KEY]);

        if ($this->helperData->getIsInvertRedirectEnable()
            && $this->isAllowRedirect($isL, $isP)
        ) {
            $defaultUrl = $this->url->getUrl('', ['_direct' => $rewrite->getRequestPath(), '_query' => $params]);
            $redirectUrl = $this->seoUrlBuilder->getPagerUrl($defaultUrl, $params, true);
            return $this->redirect($request, $redirectUrl, 301);
        }

        return $this->actionFactory->create('Magento\Framework\App\Action\Forward');
    }

    /**
     * @param string $requestUrl
     * @param array $data
     * @return bool
     */
    protected function isParsed($requestUrl, array $data)
    {
        return ($requestUrl !== $data['url']);
    }


    /**
     * @param bool $isSeoFilter
     * @param bool $isSeoPager
     * @return bool
     */
    protected function isAllowRedirect($isSeoFilter, $isSeoPager)
    {
        if ($isSeoFilter && !$this->helperData->getIsSeoFiltersEnable()) {
            return true;
        }
        if ($isSeoPager && !$this->helperData->getIsSeoPagerEnable()) {
            return true;
        }
        return false;
    }
}
