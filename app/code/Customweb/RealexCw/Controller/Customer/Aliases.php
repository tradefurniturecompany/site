<?php
/**
 * You are allowed to use this API in your web application.
 *
 * Copyright (C) 2018 by customweb GmbH
 *
 * This program is licenced under the customweb software licence. With the
 * purchase or the installation of the software in your application you
 * accept the licence agreement. The allowed usage is outlined in the
 * customweb software licence which can be found under
 * http://www.sellxed.com/en/software-license-agreement
 *
 * Any modification or distribution is strictly forbidden. The license
 * grants you the installation in one application. For multiuse you will need
 * to purchase further licences at http://www.sellxed.com/shop.
 *
 * See the customweb software licence agreement for more details.
 *
 *
 * @category	Customweb
 * @package		Customweb_RealexCw
 * 
 */

namespace Customweb\RealexCw\Controller\Customer;

class Aliases extends \Magento\Framework\App\Action\Action
{
	/**
	 * Core store config
	 *
	 * @var \Magento\Framework\App\Config\ScopeConfigInterface
	 */
	protected $_scopeConfig;

    /**
     * @var PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
    	\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
   		\Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
    	parent::__construct($context);
    	$this->_scopeConfig = $scopeConfig;
        $this->_resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
    	if ($this->_scopeConfig->getValue('realexcw/shop/alias_management') == '0') {
			return $this->resultRedirectFactory->create()->setPath('customer/account', ['_secure' => true]);
		}

        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('My Realex Aliases'));

        $block = $resultPage->getLayout()->getBlock('customer.account.link.back');
        if ($block) {
            $block->setRefererUrl($this->_redirect->getRefererUrl());
        }
        return $resultPage;
    }
}
