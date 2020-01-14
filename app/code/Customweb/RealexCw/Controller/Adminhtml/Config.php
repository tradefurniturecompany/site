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

namespace Customweb\RealexCw\Controller\Adminhtml;

abstract class Config extends \Magento\Backend\App\Action
{
	/**
	 * @var \Magento\Framework\View\Result\PageFactory
	 */
	protected $_resultPageFactory;

	/**
	 * @var \Magento\Store\Model\StoreManager
	 */
	protected $_storeManager;

	/**
	 * @var \Customweb\RealexCw\Model\Config\Structure
	 */
	protected $_formStructure;

	/**
	 * @var \Customweb\RealexCw\Model\DependencyContainer
	 */
	protected $_container;

	/**
	 * @param \Magento\Backend\App\Action\Context $context
	 * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory,
	 * @param \Magento\Store\Model\StoreManager $storeManager
	 * @param \Customweb\RealexCw\Model\Config\Structure $formStructure
	 * @param \Customweb\RealexCw\Model\DependencyContainer $container
	 */
	public function __construct(
			\Magento\Backend\App\Action\Context $context,
			\Magento\Framework\View\Result\PageFactory $resultPageFactory,
			\Magento\Store\Model\StoreManager $storeManager,
			\Customweb\RealexCw\Model\Config\Structure $formStructure,
			\Customweb\RealexCw\Model\DependencyContainer $container
	) {
		parent::__construct($context);
		$this->_resultPageFactory = $resultPageFactory;
		$this->_storeManager = $storeManager;
		$this->_formStructure = $formStructure;
		$this->_container = $container;
	}

	/**
	 * @param \Magento\Framework\App\RequestInterface $request
	 * @return \Magento\Framework\App\ResponseInterface
	 */
	public function dispatch(\Magento\Framework\App\RequestInterface $request)
	{
		if (!$request->getParam('form')) {
			$request->setParam('form', $this->_formStructure->getFirstForm()->getMachineName());
		}
		return parent::dispatch($request);
	}

	/**
	 * @return void
	 */
	protected function updateStoreHierarchy()
	{
		$websiteCode = $this->getRequest()->getParam('website');
		$storeCode   = $this->getRequest()->getParam('store');

		$configurationAdapter = $this->_container->getBean('Customweb_Payment_IConfigurationAdapter');
		$configurationAdapter->setDefaultStoreView();
		if ($websiteCode != null) {
			$configurationAdapter->setWebsite($this->_storeManager->getWebsite($websiteCode));
		}
		if ($storeCode != null) {
			$configurationAdapter->setStore($this->_storeManager->getStore($storeCode));
		}
	}

	protected function _isAllowed()
	{
		return $this->_authorization->isAllowed('Customweb_RealexCw::config');
	}
}