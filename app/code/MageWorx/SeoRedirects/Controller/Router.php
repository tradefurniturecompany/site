<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoRedirects\Controller;

use Magento\UrlRewrite\Controller\Adminhtml\Url\Rewrite;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use MageWorx\SeoRedirects\Model\Redirect\CustomRedirectFinder;

class Router extends \Magento\UrlRewrite\Controller\Router
{
    /**
     * @var \MageWorx\SeoRedirects\Helper\Data
     */
    protected $helperData;

    /**
     * @var \MageWorx\SeoRedirects\Helper\SeoUrlParser
     */
    protected $seoUrlParser;

    /**
     * @var CustomRedirectFinder
     */
    protected $customRedirectFinder;

    /**
     * Router constructor.
     *
     * @param \MageWorx\SeoRedirects\Helper\CustomRedirect\Data $helperData
     * @param CustomRedirectFinder $customRedirectFinder
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\UrlRewrite\Model\UrlFinderInterface $urlFinder
     * @param \Magento\Framework\App\ActionFactory $actionFactory
     * @param \Magento\Framework\UrlInterface $url
     * @param \Magento\Framework\App\ResponseInterface $response
     */
    public function __construct(
        \MageWorx\SeoRedirects\Helper\CustomRedirect\Data $helperData,
        CustomRedirectFinder $customRedirectFinder,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\UrlRewrite\Model\UrlFinderInterface $urlFinder,
        \Magento\Framework\App\ActionFactory $actionFactory,
        \Magento\Framework\UrlInterface $url,
        \Magento\Framework\App\ResponseInterface $response
    ) {
        $this->helperData           = $helperData;
        $this->customRedirectFinder = $customRedirectFinder;
        parent::__construct($actionFactory, $url, $storeManager, $response, $urlFinder);
    }

    public function match(\Magento\Framework\App\RequestInterface $request)
    {
        if (!$this->helperData->isEnabled()) {
            return false;
        }

        $storeId = $this->storeManager->getStore()->getId();

        /** @var \Magento\UrlRewrite\Service\V1\Data\UrlRewrite $rewrite */
        $rewrite = $this->getRewrite($request->getPathInfo(), $storeId);

        $result = $this->customRedirectFinder->getRedirectInfo($request, $storeId, $rewrite);

        if (!$result) {
            return false;
        }

        $target = $result['url'];
        $code   = $result['code'];

        if (!in_array(substr($target, 0, 6), ['http:/', 'https:'])) {
            $target = $this->url->getUrl('', ['_direct' => ltrim($target, '/')]);
        }

        return $this->redirect($request, $target, $code);
    }
}
