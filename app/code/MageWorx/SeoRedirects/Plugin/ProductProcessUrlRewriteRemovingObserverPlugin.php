<?php
/**
 * Copyright © 2015 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoRedirects\Plugin;

use Ess\M2ePro\Model\Exception;
use Magento\Framework\Exception\LocalizedException;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\UrlRewrite\Model\UrlFinderInterface;
use Magento\CatalogUrlRewrite\Model\ProductUrlRewriteGenerator;
use MageWorx\SeoRedirects\Model\Redirect\CustomRedirect;
use MageWorx\SeoRedirects\Model\Redirect\DpRedirect\RedirectCreatorByDeletedProductUrl;
use MageWorx\SeoRedirects\Model\Redirect\CustomRedirect\RedirectWizardByDeletedUrlFactory;

class ProductProcessUrlRewriteRemovingObserverPlugin
{
    /**
     * @var UrlFinderInterface
     */
    protected $urlFinder;

    /**
     * @var RedirectWizardByDeletedUrlFactory
     */
    protected $redirectWizardByDeletedProductUrl;

    /**
     * @var RedirectWizardByDeletedUrlFactory
     */
    protected $redirectWizardByDeletedUrlFactory;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \MageWorx\SeoRedirects\Helper\CustomRedirect\Data
     */
    protected $helperData;

    /**
     * ProductProcessUrlRewriteRemovingObserverBefore constructor.
     *
     * @param UrlFinderInterface $urlFinder
     * @param RedirectWizardByDeletedUrlFactory $redirectWizardByDeletedUrlFactory
     * @param RedirectCreatorByDeletedProductUrl $redirectCreatorByDeletedProductUrl
     */
    public function __construct(
        \Magento\UrlRewrite\Model\UrlFinderInterface $urlFinder,
        RedirectWizardByDeletedUrlFactory $redirectWizardByDeletedUrlFactory,
        RedirectCreatorByDeletedProductUrl $redirectCreatorByDeletedProductUrl,
        \MageWorx\SeoRedirects\Helper\CustomRedirect\Data $helperData,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        $this->urlFinder                          = $urlFinder;
        $this->redirectWizardByDeletedUrlFactory  = $redirectWizardByDeletedUrlFactory;
        $this->redirectCreatorByDeletedProductUrl = $redirectCreatorByDeletedProductUrl;
        $this->helperData                         = $helperData;
        $this->messageManager                     = $messageManager;
    }

    /**
     * @param \Magento\CatalogUrlRewrite\Observer\ProductProcessUrlRewriteRemovingObserver $subject
     * @param \Magento\Framework\Event\Observer $observer
     * @return array|void
     * @throws \Exception
     */
    public function beforeExecute($subject, $observer)
    {
        /** @var \Magento\Catalog\Api\Data\ProductInterface $product */
        $product = $observer->getEvent()->getProduct();

        if (!$product) {
            return [$observer];
        }

        //duplicated and non-saved product
        if (!$product->getSku() || !$product->getId()) {
            return [$observer];
        }

        $currentUrlRewrites = $this->urlFinder->findAllByData(
            [
                UrlRewrite::ENTITY_ID   => $product->getId(),
                UrlRewrite::ENTITY_TYPE => ProductUrlRewriteGenerator::ENTITY_TYPE,
            ]
        );

        if (!$currentUrlRewrites) {
            return [$observer];
        }

        if ($this->helperData->isKeepUrlsForDeletedEntities()) {
            try {
                //Create or update for custom redirects
                /** @var \MageWorx\SeoRedirects\Model\Redirect\CustomRedirect\RedirectWizardByDeletedUrlAbstract $redirectWizardByDeletedUrl */
                $redirectWizardByDeletedUrl = $this->redirectWizardByDeletedUrlFactory->create(
                    CustomRedirect::REDIRECT_TYPE_PRODUCT
                );
                $redirectWizardByDeletedUrl->process($product->getId(), $currentUrlRewrites);
            } catch (LocalizedException $e) {
                $this->messageManager->addNoticeMessage($e->getMessage());
            }
        }

        //Create redirects for deleted products
        $this->redirectCreatorByDeletedProductUrl->process($product, $currentUrlRewrites);

        return [$observer];
    }
}
