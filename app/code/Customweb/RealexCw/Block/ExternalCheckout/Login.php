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

namespace Customweb\RealexCw\Block\ExternalCheckout;

class Login extends AbstractExternalCheckout
{
	/**
	 * @var \Magento\Checkout\Helper\Data
	 */
	protected $_checkoutHelper;

	/**
	 * @var \Magento\Customer\Model\Session
	 */
	protected $_customerSession;

	/**
	 * @var \Magento\Customer\Model\Url
	 */
	protected $_customerUrl;

	/**
	 * @var \Magento\Customer\Model\Registration
	 */
	protected $_registration;

	/**
	 * @var string
	 */
	protected $_template = 'Customweb_RealexCw::externalcheckout/login.phtml';

	/**
	 * @param \Magento\Framework\View\Element\Template\Context $context
	 * @param \Magento\Checkout\Helper\Data $checkoutHelper
	 * @param \Magento\Customer\Model\Session $customerSession
	 * @param \Magento\Customer\Model\Url $customerUrl
	 * @param \Magento\Customer\Model\Registration $registration
	 * @param array $data
	 */
	public function __construct(
			\Magento\Framework\View\Element\Template\Context $context,
			\Magento\Checkout\Helper\Data $checkoutHelper,
			\Magento\Customer\Model\Session $customerSession,
			\Magento\Customer\Model\Url $customerUrl,
			\Magento\Customer\Model\Registration $registration,
			array $data = []
	) {
		parent::__construct($context, $data);
		$this->_checkoutHelper = $checkoutHelper;
		$this->_customerSession = $customerSession;
		$this->_customerUrl = $customerUrl;
		$this->_registration = $registration;
	}

	/**
     * @return string
     */
	public function getForgotPasswordUrl()
	{
		return $this->_customerUrl->getForgotPasswordUrl();
	}

	/**
	 * @return string
	 */
	public function getLoginPostAction()
	{
		return $this->getUrl('realexcw/externalCheckout/loginPost', ['_secure' => true]);
	}

	/**
	 * @return string
	 */
	public function getGuestPostAction()
	{
		return $this->getUrl('realexcw/externalCheckout/guestPost', ['_secure' => true]);
	}

	/**
     * @return string
     */
	public function getRegisterPostAction()
	{
		return $this->getUrl('realexcw/externalCheckout/registerPost', ['_secure' => true]);
	}

	/**
	 * @return \Magento\Customer\Model\Registration
	 */
	public function getRegistration()
	{
		return $this->_registration;
	}

	/**
	 * @return string
	 */
	public function getUsername()
	{
		$username = $this->_customerSession->getUsername(true);
		if ($username) {
			return $username;
		} else {
			return $this->getContext()->getAuthenticationEmailAddress();
		}
	}

	/**
	 * @return boolean
	 */
	public function isAllowedGuestCheckout()
	{
		return $this->_checkoutHelper->isAllowedGuestCheckout($this->getContext()->getQuote());
	}
}