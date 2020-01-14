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

namespace Customweb\RealexCw\Controller;

abstract class ExternalCheckout extends \Magento\Framework\App\Action\Action
{
	/**
	 * @var \Magento\Checkout\Model\Session
	 */
	protected $_checkoutSession;

	/**
	 * \Customweb\RealexCw\Model\ExternalCheckout\ContextFactory
	 */
	protected $_contextFactory;

	/**
	 * @var \Customweb\RealexCw\Model\ExternalCheckout\Context
	 */
	private $context;

	/**
	 * @param \Magento\Framework\App\Action\Context $context
	 * @param \Magento\Checkout\Model\Session $checkoutSession
	 * @param \Customweb\RealexCw\Model\ExternalCheckout\ContextFactory $contextFactory
	 */
	public function __construct(
			\Magento\Framework\App\Action\Context $context,
			\Magento\Checkout\Model\Session $checkoutSession,
			\Customweb\RealexCw\Model\ExternalCheckout\ContextFactory $contextFactory
	) {
		parent::__construct($context);
		$this->_checkoutSession = $checkoutSession;
		$this->_contextFactory = $contextFactory;
	}

	/**
	 * @return \Magento\Quote\Model\Quote
	 */
	public function getQuote()
	{
		return $this->_checkoutSession->getQuote();
	}

	/**
	 * @return \Customweb\RealexCw\Model\ExternalCheckout\Context
	 */
	public function getContext()
	{
		if (!($this->context instanceof \Customweb\RealexCw\Model\ExternalCheckout\Context)) {
			$this->context = $this->_contextFactory->create()->loadByQuoteId($this->getQuote()->getId());
		}
		return $this->context;
	}
}