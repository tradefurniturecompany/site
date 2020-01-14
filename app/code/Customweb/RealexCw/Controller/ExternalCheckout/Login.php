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

namespace Customweb\RealexCw\Controller\ExternalCheckout;

class Login extends \Customweb\RealexCw\Controller\ExternalCheckout
{
	/**
	 * @var \Magento\Customer\Model\Session
	 */
	protected $_customerSession;

	public function __construct(
			\Magento\Framework\App\Action\Context $context,
			\Magento\Checkout\Model\Session $checkoutSession,
			\Magento\Customer\Model\Session $customerSession,
			\Customweb\RealexCw\Model\ExternalCheckout\ContextFactory $contextFactory
	) {
		parent::__construct($context, $checkoutSession, $contextFactory);
		$this->_customerSession = $customerSession;
	}

	public function execute()
	{
		if (!($this->getContext() instanceof \Customweb\RealexCw\Model\ExternalCheckout\Context) || !$this->getContext()->getId()) {
			return $this->resultRedirectFactory->create()->setPath('checkout/cart');
		}

		if ($this->_customerSession->isLoggedIn()) {
			return $this->resultRedirectFactory->create()->setUrl($this->getContext()->getAuthenticationSuccessUrl());
		}

		$this->_view->loadLayout();
		$this->_view->getLayout()
			->getBlock('customweb_realexcwexternalcheckout_login')
			->setContext($this->getContext());
		$this->_view->renderLayout();
	}
}