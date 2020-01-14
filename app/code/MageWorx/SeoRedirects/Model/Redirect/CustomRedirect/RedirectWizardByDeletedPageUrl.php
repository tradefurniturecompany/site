<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoRedirects\Model\Redirect\CustomRedirect;

use MageWorx\SeoRedirects\Model\ResourceModel\Redirect\CustomRedirect\CollectionFactory as CustomRedirectCollectionFactory;
use MageWorx\SeoRedirects\Model\Redirect\CustomRedirectFactory;
use MageWorx\SeoRedirects\Model\Redirect\CustomRedirect;
use MageWorx\SeoRedirects\Model\Redirect\Source\CustomRedirect\RedirectTypeRewriteFragment as RedirectTypeRewriteFragmentSource;
use Magento\Framework\Message\ManagerInterface;

class RedirectWizardByDeletedPageUrl extends RedirectWizardByDeletedUrlAbstract
{
    /**
     * @var \MageWorx\SeoAll\Helper\Page
     */
    protected $helperPage;
    /**
     * @var int
     */
    protected $redirectEntityType = CustomRedirect::REDIRECT_TYPE_PAGE;

    /**
     * RedirectWizardByDeletedUrlAbstract constructor.
     *
     * @param CustomRedirectCollectionFactory $customRedirectCollectionFactory
     * @param CustomRedirectFactory $customRedirectFactory
     * @param RedirectTypeRewriteFragmentSource $redirectTypeRewriteFragmentSource
     * @param ManagerInterface $messageManager
     * @param \MageWorx\SeoRedirects\Helper\Data $helperData
     */
    public function __construct(
        CustomRedirectCollectionFactory $customRedirectCollectionFactory,
        CustomRedirectFactory $customRedirectFactory,
        RedirectTypeRewriteFragmentSource $redirectTypeRewriteFragmentSource,
        ManagerInterface $messageManager,
        \MageWorx\SeoRedirects\Helper\CustomRedirect\Data $helperData,
        \MageWorx\SeoAll\Helper\Page $helperPage
    ) {
        parent::__construct(
            $customRedirectCollectionFactory,
            $customRedirectFactory,
            $redirectTypeRewriteFragmentSource,
            $messageManager,
            $helperData
        );
        $this->helperPage = $helperPage;
    }

    /**
     * {@inheritdoc}
     */
    protected function prepareTargetUrl($url, $entityId = null, $storeId = null)
    {
        if ($entityId && $storeId) {
            if ($this->helperPage->getIsHomePage($url, $entityId, $storeId)) {
                return '';
            }
        }

        return $this->helperData->addTrailingSlash($url);
    }
}
