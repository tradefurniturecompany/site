<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoRedirects\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\RedirectFactory;
use MageWorx\SeoRedirects\Model\Redirect\CustomRedirectFactory;
use Magento\Framework\Registry;
use MageWorx\SeoRedirects\Model\Redirect\CustomRedirectRepository;

abstract class CustomRedirect extends Action
{
    /**
     * Redirect repository
     *
     * @var CustomRedirectRepository
     */
    protected $customRedirectRepository;

    /**
     * Core registry
     *
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * @var RedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * @param Registry $registry
     * @param CustomRedirectFactory $customRedirectFactory
     * @param Context $context
     */
    public function __construct(
        Registry $registry,
        CustomRedirectRepository $customRedirectRepository,
        Context $context
    ) {
        $this->coreRegistry             = $registry;
        $this->customRedirectRepository = $customRedirectRepository;
        $this->resultRedirectFactory    = $context->getResultRedirectFactory();
        parent::__construct($context);
    }

    /**
     * @return \MageWorx\SeoRedirects\Model\Redirect\CustomRedirect
     */
    protected function initCustomRedirect()
    {
        $redirectId = $this->getRequest()->getParam('redirect_id');

        $customRedirect = $this->customRedirectFactory->create();
        if ($customRedirect) {
            $customRedirect->load($redirectId);
        }
        $this->coreRegistry->register('mageworx_seoredirects_customredirect', $customRedirect);

        return $customRedirect;
    }

    /**
     * filter dates
     *
     * @param array $data
     * @return array
     */
    public function filterData($data)
    {
        return $data;
    }

    /**
     * Is access to section allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('MageWorx_SeoRedirects::customredirects');
    }
}
