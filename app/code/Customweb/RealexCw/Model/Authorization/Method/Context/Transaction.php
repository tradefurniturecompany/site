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

class Transaction extends AbstractContext
{
	/**
	 * @var \Customweb\RealexCw\Model\Authorization\TransactionFactory
	 */
	protected $_transactionFactory;

	/**
	 * @var \Customweb\RealexCw\Model\Authorization\Transaction
	 */
	protected $transaction;

	/**
	 * @param \Magento\Framework\Registry $coreRegistry
	 * @param \Magento\Checkout\Model\Session $checkoutSession
	 * @param \Magento\Framework\App\RequestInterface $request
	 * @param \Customweb\RealexCw\Model\Authorization\OrderContextFactory $orderContextFactory
	 * @param \Customweb\RealexCw\Model\Authorization\CustomerContextFactory $customerContextFactory
	 * @param \Customweb\RealexCw\Model\Authorization\TransactionFactory $transactionFactory
	 * @param \Customweb\RealexCw\Model\Authorization\Transaction $transaction
	 * @param \Magento\Sales\Model\Order $order
	 * @param array $parameters
	 */
	public function __construct(
			\Magento\Framework\Registry $coreRegistry,
			\Magento\Checkout\Model\Session $checkoutSession,
			\Magento\Framework\App\RequestInterface $request,
			\Customweb\RealexCw\Model\Authorization\OrderContextFactory $orderContextFactory,
			\Customweb\RealexCw\Model\Authorization\CustomerContextFactory $customerContextFactory,
			\Customweb\RealexCw\Model\Authorization\TransactionFactory $transactionFactory,
			\Customweb\RealexCw\Model\Authorization\Transaction $transaction = null,
			\Magento\Sales\Model\Order $order = null,
			array $parameters = null
	) {
		parent::__construct($coreRegistry, $checkoutSession, $request, $orderContextFactory, $customerContextFactory);
		$this->_transactionFactory = $transactionFactory;

		if (!($transaction instanceof \Customweb\RealexCw\Model\Authorization\Transaction)) {
			$orderId = null;
			if ($order instanceof \Magento\Sales\Model\Order) {
				$orderId = $order->getId();
			} else {
				$orderId = $this->_checkoutSession->getLastRealOrder()->getId();
			}
			$transaction = $this->_transactionFactory->create()->loadByOrderId($orderId);
			if (!($transaction instanceof \Customweb\RealexCw\Model\Authorization\Transaction)) {
				throw new \Exception('The transaction for order ' . $orderId . ' could not have been created.');
			}
		}
		$this->transaction = $transaction;

		$this->parameters = $parameters;
	}

	public function getPaymentMethod()
	{
		return $this->getTransaction()->getTransactionObject()->getPaymentMethod();
	}

	public function getOrderContext()
	{
		return $this->getTransaction()->getTransactionObject()->getTransactionContext()->getOrderContext();
	}

	public function getTransaction()
	{
		return $this->transaction;
	}

	public function getOrder()
	{
		return $this->getTransaction()->getOrder();
	}

	public function getQuote()
	{
		return $this->getTransaction()->getQuote();
	}

	public function isMoto()
	{
		return $this->getTransaction()->getTransactionObject() != null
			&& $this->getTransaction()->getTransactionObject()->getAuthorizationMethod() == \Customweb_Payment_Authorization_Moto_IAdapter::AUTHORIZATION_METHOD_NAME;
	}
}