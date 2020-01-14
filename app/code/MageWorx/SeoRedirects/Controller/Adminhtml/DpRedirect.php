<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoRedirects\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\RedirectFactory;
use MageWorx\SeoRedirects\Model\Redirect\DpRedirectFactory;
use Magento\Framework\Registry;

abstract class DpRedirect extends Action
{
    /**
     * Redirect factory
     *
     * @var RedirectFactory
     */
    protected $dpRedirectFactory;

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
     * @param DpRedirectFactory $dpRedirectFactory
     * @param Context $context
     */
    public function __construct(
        Registry $registry,
        DpRedirectFactory $dpRedirectFactory,
        Context $context
    ) {

        $this->coreRegistry          = $registry;
        $this->dpRedirectFactory     = $dpRedirectFactory;
        $this->resultRedirectFactory = $context->getResultRedirectFactory();
        parent::__construct($context);
    }

    /**
     * @return \MageWorx\SeoRedirects\Model\Redirect\DpRedirect
     */
    protected function initDpRedirect()
    {
        $redirectId = $this->getRequest()->getParam('redirect_id');

        $dpRedirect = $this->dpRedirectFactory->create();
        if ($dpRedirect) {
            $dpRedirect->load($redirectId);
        }
        $this->coreRegistry->register('mageworx_seoredirects_dpredirect', $dpRedirect);

        return $dpRedirect;
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
        return $this->_authorization->isAllowed('MageWorx_SeoRedirects::dpredirects');
    }
}
