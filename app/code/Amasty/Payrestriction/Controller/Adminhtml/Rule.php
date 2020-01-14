<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Payrestriction
 */


namespace Amasty\Payrestriction\Controller\Adminhtml;

/**
 * Items controller
 */
abstract class Rule extends \Magento\Backend\App\Action
{
    const FIELDS = [
        'stores',
        'cust_groups',
        'methods',
        'days',
        'discount_id',
        'discount_id_disable'
    ];

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     *  @var \Magento\Framework\View\Result\LayoutFactory
    */
    protected $_resultLayoutFactory = null;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
        $this->resultPageFactory = $resultPageFactory;
        $this->_resultLayoutFactory = $resultLayoutFactory;
    }

    /**
     * @return \Magento\Framework\View\Result\Page
     */
    protected function _initAction()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Amasty_Payrestriction::sales_restiction');
        $resultPage->addBreadcrumb(__('Payment Restrictions'), __('Payment Restrictions'));
        $resultPage->getConfig()->getTitle()->prepend(__('Payment Restrictions'));

        return $resultPage;
    }

    /**
     * Determine if authorized to perform group actions.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Amasty_Payrestriction::sales_restiction');
    }
}
