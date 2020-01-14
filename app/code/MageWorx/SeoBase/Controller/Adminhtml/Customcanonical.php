<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoBase\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Psr\Log\LoggerInterface;
use Magento\Store\Model\StoreManagerInterface as StoreManager;
use MageWorx\SeoBase\Api\CustomCanonicalRepositoryInterface;
use MageWorx\SeoBase\Helper\CustomCanonical as HelperCustomCanonical;

abstract class Customcanonical extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'MageWorx_SeoBase::manage_canonical_urls';

    /**
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var StoreManager
     */
    protected $storeManager;

    /**
     * @var CustomCanonicalRepositoryInterface
     */
    protected $customCanonicalRepository;

    /**
     * @var HelperCustomCanonical
     */
    protected $helperCustomCanonical;

    /**
     * Customcanonical constructor.
     *
     * @param Context $context
     * @param Registry $coreRegistry
     * @param LoggerInterface $logger
     * @param StoreManager $storeManager
     * @param CustomCanonicalRepositoryInterface $customCanonicalRepository
     * @param HelperCustomCanonical $helperCustomCanonical
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        LoggerInterface $logger,
        StoreManager $storeManager,
        CustomCanonicalRepositoryInterface $customCanonicalRepository,
        HelperCustomCanonical $helperCustomCanonical
    ) {
        $this->coreRegistry              = $coreRegistry;
        $this->logger                    = $logger;
        $this->storeManager              = $storeManager;
        $this->customCanonicalRepository = $customCanonicalRepository;
        $this->helperCustomCanonical     = $helperCustomCanonical;
        parent::__construct($context);
    }

    /**
     * @return $this
     */
    protected function _initAction()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu(
            self::ADMIN_RESOURCE
        )->_addBreadcrumb(
            __('Marketing'),
            __('Marketing')
        )->_addBreadcrumb(
            __('MageWorx Canonical URLs'),
            __('MageWorx Canonical URLs')
        )->_addBreadcrumb(
            __('Сustom Сanonical URLs'),
            __('Сustom Сanonical URLs')
        );

        return $this;
    }
}
