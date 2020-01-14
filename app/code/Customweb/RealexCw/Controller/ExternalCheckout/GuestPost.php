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

class GuestPost extends \Customweb\RealexCw\Controller\ExternalCheckout
{
	/**
	 * @var \Magento\Customer\Model\Session
	 */
	protected $_customerSession;

	/**
	 * @var \Magento\Framework\Data\Form\FormKey\Validator
	 */
	protected $_formKeyValidator;

	/**
	 * @var \Customweb\RealexCw\Helper\ExternalCheckout
	 */
	protected $_helper;

	/**
	 * @param \Magento\Framework\App\Action\Context $context
	 * @param \Magento\Checkout\Model\Session $checkoutSession
	 * @param \Magento\Customer\Model\Session $customerSession
	 * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
	 * @param \Customweb\RealexCw\Helper\ExternalCheckout $helper
	 * @param \Customweb\RealexCw\Model\ExternalCheckout\ContextFactory $contextFactory
	 */
	public function __construct(
			\Magento\Framework\App\Action\Context $context,
			\Magento\Checkout\Model\Session $checkoutSession,
			\Magento\Customer\Model\Session $customerSession,
			\Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
			\Customweb\RealexCw\Model\ExternalCheckout\ContextFactory $contextFactory,
			\Customweb\RealexCw\Helper\ExternalCheckout $helper
	) {
		parent::__construct($context, $checkoutSession, $contextFactory);
		$this->_customerSession = $customerSession;
		$this->_formKeyValidator = $formKeyValidator;
		$this->_helper = $helper;
	}

	public function execute()
	{
		if (!($this->getContext() instanceof \Customweb\RealexCw\Model\ExternalCheckout\Context) || !$this->getContext()->getId()) {
			return $this->resultRedirectFactory->create()->setPath('checkout/cart');
		}

		if ($this->_customerSession->isLoggedIn()) {
			return $this->resultRedirectFactory->create()->setUrl($this->getContext()->getAuthenticationSuccessUrl());
		}

		if (!$this->_formKeyValidator->validate($this->getRequest())) {
			return $this->resultRedirectFactory->create()->setPath('*/*/login');
		}

		if ($this->getRequest()->isPost()) {
			$this->getContext()->setRegisterMethod(\Customweb\RealexCw\Model\ExternalCheckout\Context::REGISTER_METHOD_GUEST);
			$data = [
				'email' => $this->getContext()->getAuthenticationEmailAddress(),
				'firstname' => $this->getContext()->getBillingAddress()->getFirstName(),
				'lastname' => $this->getContext()->getBillingAddress()->getLastName(),
			];

			if (true !== ($result = $this->_helper->validateCustomerData($this->getQuote(), $data, \Customweb\RealexCw\Model\ExternalCheckout\Context::REGISTER_METHOD_GUEST))) {
				$this->messageManager->addError($result);
				return $this->resultRedirectFactory->create()->setPath('*/*/login');
			}

			$this->getQuote()->collectTotals()->save();

			$this->getContext()->updateQuote($this->getQuote())->save();

			return $this->resultRedirectFactory->create()->setUrl($this->getContext()->getAuthenticationSuccessUrl());
		}

		return $this->resultRedirectFactory->create()->setPath('*/*/login');
	}
}