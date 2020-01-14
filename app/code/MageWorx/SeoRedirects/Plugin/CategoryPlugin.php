<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace MageWorx\SeoRedirects\Plugin;

use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\CatalogUrlRewrite\Model\Category\ChildrenCategoriesProvider;
use Magento\CatalogUrlRewrite\Model\CategoryUrlRewriteGenerator;
use Magento\CatalogUrlRewrite\Model\ProductUrlRewriteGenerator;
use Magento\UrlRewrite\Model\UrlPersistInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;

use Magento\UrlRewrite\Model\UrlFinderInterface;
use MageWorx\SeoRedirects\Model\Redirect\CustomRedirect;
use MageWorx\SeoRedirects\Model\Redirect\CustomRedirect\RedirectWizardByDeletedUrlFactory;
use Magento\Framework\Exception\LocalizedException;

class CategoryPlugin extends \Magento\CatalogUrlRewrite\Model\Category\Plugin\Category\Remove
{
    /**
     * @var UrlFinderInterface
     */
    protected $urlFinder;

    /**
     * @var RedirectWizardByDeletedUrlFactory
     */
    protected $redirectWizardByDeletedUrlFactory;

    /**
     * @var \MageWorx\SeoRedirects\Helper\CustomRedirect\Data
     */
    protected $helperData;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * CategoryPlugin constructor.
     *
     * @param UrlPersistInterface $urlPersist
     * @param ProductUrlRewriteGenerator $productUrlRewriteGenerator
     * @param ChildrenCategoriesProvider $childrenCategoriesProvider
     * @param UrlFinderInterface $urlFinder
     * @param RedirectWizardByDeletedUrlFactory $redirectWizardByDeletedUrlFactory
     */
    public function __construct(
        UrlPersistInterface $urlPersist,
        ProductUrlRewriteGenerator $productUrlRewriteGenerator,
        ChildrenCategoriesProvider $childrenCategoriesProvider,
        \Magento\UrlRewrite\Model\UrlFinderInterface $urlFinder,
        RedirectWizardByDeletedUrlFactory $redirectWizardByDeletedUrlFactory,
        \MageWorx\SeoRedirects\Helper\CustomRedirect\Data $helperData,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        parent::__construct($urlPersist, $productUrlRewriteGenerator, $childrenCategoriesProvider);
        $this->urlFinder                         = $urlFinder;
        $this->redirectWizardByDeletedUrlFactory = $redirectWizardByDeletedUrlFactory;
        $this->helperData                        = $helperData;
        $this->messageManager                    = $messageManager;
    }

    /**
     * Remove url rewrites by categoryId
     *
     * @param int $categoryId
     * @return void
     */
    protected function deleteRewritesForCategory($categoryId)
    {
        if ($this->helperData->isKeepUrlsForDeletedEntities()) {
            $currentUrlRewrites = $this->urlFinder->findAllByData(
                [
                    UrlRewrite::ENTITY_ID   => $categoryId,
                    UrlRewrite::ENTITY_TYPE => CategoryUrlRewriteGenerator::ENTITY_TYPE,
                ]
            );

            if ($currentUrlRewrites) {
                try {
                    $redirectWizardByDeletedUrl = $this->redirectWizardByDeletedUrlFactory->create(
                        CustomRedirect::REDIRECT_TYPE_CATEGORY
                    );
                    $redirectWizardByDeletedUrl->process($categoryId, $currentUrlRewrites);
                } catch (LocalizedException $e) {
                    $this->messageManager->addNoticeMessage($e->getMessage());
                }
            }

            $currentUrlRewrites = $this->urlFinder->findAllByData(
                [
                    UrlRewrite::METADATA    => serialize(['category_id' => $categoryId]),
                    UrlRewrite::ENTITY_TYPE => CategoryUrlRewriteGenerator::ENTITY_TYPE,
                ]
            );

            if ($currentUrlRewrites) {
                try {
                    $redirectWizardByDeletedUrl = $this->redirectWizardByDeletedUrlFactory->create(
                        CustomRedirect::REDIRECT_TYPE_CATEGORY
                    );
                    $redirectWizardByDeletedUrl->process($categoryId, $currentUrlRewrites);
                } catch (LocalizedException $e) {
                    $this->messageManager->addNoticeMessage($e->getMessage());
                }
            }
        }

        return parent::deleteRewritesForCategory($categoryId);
    }
}
