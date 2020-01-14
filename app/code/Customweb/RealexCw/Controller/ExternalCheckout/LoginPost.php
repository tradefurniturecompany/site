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

class LoginPost extends \Customweb\RealexCw\Controller\ExternalCheckout
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
	 * @var \Magento\Customer\Api\AccountManagementInterface
	 */
	protected $_customerAccountManagement;

	/**
	 * @var \Magento\Customer\Model\Url
	 */
	protected $_customerUrl;

	public function __construct(
			\Magento\Framework\App\Action\Context $context,
			\Magento\Checkout\Model\Session $checkoutSession,
			\Magento\Customer\Model\Session $customerSession,
			\Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
			\Magento\Customer\Api\AccountManagementInterface $customerAccountManagement,
			\Magento\Customer\Model\Url $customerUrl,
			\Customweb\RealexCw\Model\ExternalCheckout\ContextFactory $contextFactory
	) {
		parent::__construct($context, $checkoutSession, $contextFactory);
		$this->_customerSession = $customerSession;
		$this->_formKeyValidator = $formKeyValidator;
		$this->_customerAccountManagement = $customerAccountManagement;
		$this->_customerUrl = $customerUrl;
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
			$login = $this->getRequest()->getPost('login');
			if (!empty($login['username']) && !empty($login['password'])) {
				try {
					$customer = $this->_customerAccountManagement->authenticate($login['username'], $login['password']);
					$this->_customerSession->setCustomerDataAsLoggedIn($customer);
					$this->_customerSession->regenerateId();
				} catch (\Magento\Framework\Exception\EmailNotConfirmedException $e) {
					$value = $this->_customerUrl->getEmailConfirmationUrl($login['username']);
					$message = __(
							'This account is not confirmed.' .
							' <a href="%1">Click here</a> to resend confirmation email.',
							$value
					);
					$this->messageManager->addError($message);
					$this->_customerSession->setUsername($login['username']);
				}
				catch (\Magento\Framework\Exception\AuthenticationException $e) {
					$message = __('Invalid login or password.');
					$this->messageManager->addError($message);
					$this->_customerSession->setUsername($login['username']);
				} catch (\Exception $e) {
					$this->messageManager->addError(__('There was an error validating the login and password.'));
				}
			} else {
				$this->messageManager->addError(__('Login and password are required.'));
			}
		}

		if ($this->_customerSession->isLoggedIn()) {
			$this->getContext()->updateQuote($this->getQuote())->save();

			return $this->resultRedirectFactory->create()->setUrl($this->getContext()->getAuthenticationSuccessUrl());
		}

		return $this->resultRedirectFactory->create()->setPath('*/*/login');
	}
}