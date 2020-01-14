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

class DeleteAlias extends \Magento\Framework\App\Action\Action
{
	/**
	 * Core store config
	 *
	 * @var \Magento\Framework\App\Config\ScopeConfigInterface
	 */
	protected $_scopeConfig;

    /**
     * @var \Customweb\RealexCw\Model\Authorization\TransactionFactory
     */
    protected $_transactionFactory;

    /**
     * @var \Customweb\RealexCw\Model\Alias\Handler
     */
    protected $_aliasHandler;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Customweb\RealexCw\Model\Authorization\TransactionFactory $transactionFactory
     * @param \Customweb\RealexCw\Model\Alias\Handler $aliasHandler
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
    	\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Customweb\RealexCw\Model\Authorization\TransactionFactory $transactionFactory,
    	\Customweb\RealexCw\Model\Alias\Handler $aliasHandler
    ) {
    	parent::__construct($context);
    	$this->_scopeConfig = $scopeConfig;
        $this->_transactionFactory = $transactionFactory;
    	$this->_aliasHandler = $aliasHandler;
    }

    public function execute()
    {
    	if ($this->_scopeConfig->getValue('realexcw/shop/alias_management') == '0') {
    		return $this->resultRedirectFactory->create()->setPath('customer/account', ['_secure' => true]);
    	}

        /* @var $transaction \Customweb\RealexCw\Model\Authorization\Transaction */
        $transaction = $this->_transactionFactory->create()->load($this->getRequest()->getParam('id'));
        if ($transaction->getId()) {
        	$this->_aliasHandler->removeAlias($transaction);
        	$this->messageManager->addSuccess(__("The alias has been deleted."));
        } else {
        	$this->messageManager->addError(__("The alias could not be found."));
        }

        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('*/*/aliases');
        return $resultRedirect;
    }
}
