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

namespace Customweb\RealexCw\Model\Authorization\Method\Context;

class Order extends AbstractContext
{
	/**
	 * @var \Magento\Quote\Model\QuoteFactory
	 */
	protected $_quoteFactory;

	/**
	 * @var \Magento\Sales\Model\Order
	 */
	protected $order;

	/**
	 * @var \Magento\Quote\Model\Quote
	 */
	protected $quote;

	/**
	 * @var \Customweb\RealexCw\Model\Authorization\OrderContext
	 */
	protected $orderContext;

	/**
	 * @param \Magento\Checkout\Model\Session $checkoutSession
	 * @param \Magento\Framework\App\RequestInterface $request
	 * @param \Customweb\RealexCw\Model\Authorization\OrderContextFactory $orderContextFactory
	 * @param \Customweb\RealexCw\Model\Authorization\CustomerContextFactory $customerContextFactory
	 * @param \Magento\Quote\Model\QuoteFactory $quoteFactory
	 * @param \Magento\Sales\Model\Order $order
	 */
	public function __construct(
			\Magento\Framework\Registry $coreRegistry,
			\Magento\Checkout\Model\Session $checkoutSession,
			\Magento\Framework\App\RequestInterface $request,
			\Customweb\RealexCw\Model\Authorization\OrderContextFactory $orderContextFactory,
			\Customweb\RealexCw\Model\Authorization\CustomerContextFactory $customerContextFactory,
			\Magento\Quote\Model\QuoteFactory $quoteFactory,
			\Magento\Sales\Model\Order $order = null
	) {
		parent::__construct($coreRegistry, $checkoutSession, $request, $orderContextFactory, $customerContextFactory);
		$this->_quoteFactory = $quoteFactory;

		if (!($order instanceof \Magento\Sales\Model\Order)) {
			$order = $this->_checkoutSession->getLastRealOrder();
		}
		$this->order = $order;
	}

	public function getPaymentMethod()
	{
		return $this->getOrder()->getPayment()->getMethodInstance();
	}

	public function getOrderContext()
	{
		if (!($this->orderContext instanceof \Customweb\RealexCw\Model\Authorization\OrderContext)) {
			$this->orderContext = $this->_orderContextFactory->create(['order' => $this->getOrder(), 'paymentMethod' => $this->getPaymentMethod()]);
		}
		return $this->orderContext;
	}

	public function getTransaction()
	{
		throw new \Exception("No transaction available yet.");
	}

	public function getOrder()
	{
		return $this->order;
	}

	public function getQuote()
	{
		if (!($this->quote instanceof \Magento\Quote\Model\Quote)) {
			$this->quote = $this->_quoteFactory->create()->load($this->getOrder()->getQuoteId());
		}
		return $this->quote;
	}

	public function isMoto()
	{
		return $this->_coreRegistry->registry('realexcwcheckout_moto');
	}
}