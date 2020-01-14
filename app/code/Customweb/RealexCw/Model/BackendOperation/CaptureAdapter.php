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

namespace Customweb\RealexCw\Model\BackendOperation;

class CaptureAdapter implements \Customweb_Payment_BackendOperation_Adapter_Shop_ICapture
{
	/**
	 * @var \Customweb\RealexCw\Model\Authorization\TransactionFactory
	 */
	protected $_transactionFactory;

	/**
	 * @param \Customweb\RealexCw\Model\Authorization\TransactionFactory $transactionFactory
	 */
	public function __construct(
			\Customweb\RealexCw\Model\Authorization\TransactionFactory $transactionFactory
	) {
		$this->_transactionFactory = $transactionFactory;
	}

	public function capture(\Customweb_Payment_Authorization_ITransaction $transaction)
	{
		$this->registerCapture($transaction, $transaction->getTransactionContext()->getOrderContext()->getOrderAmountInDecimals());
	}

	public function partialCapture(\Customweb_Payment_Authorization_ITransaction $transaction, $items, $close)
	{
		$this->registerCapture($transaction, \Customweb_Util_Invoice::getTotalAmountIncludingTax($items));
	}

	/**
	 * @param \Customweb_Payment_Authorization_ITransaction $transaction
	 * @param float $amount
	 */
	private function registerCapture(\Customweb_Payment_Authorization_ITransaction $transaction, $amount)
	{
		/* @var $transactionEntity \Customweb\RealexCw\Model\Authorization\Transaction */
		$transactionEntity = $this->_transactionFactory->create()->load($transaction->getTransactionId());
		if (!$transactionEntity->getId()) {
			throw new \Exception('The transaction has not been found.');
		}

		if ($transactionEntity->getOrderPayment()->getAuthorizationTransaction() instanceof \Magento\Sales\Model\Order\Payment\Transaction) {
			$transactionEntity->getOrderPayment()->setShouldCloseParentTransaction(true);
			// Fix for Magento bug: https://github.com/magento/magento2/issues/18148
			$transactionEntity->getOrderPayment()->getOrder()->setState(null);
			$transactionEntity->getOrderPayment()->registerCaptureNotification($amount, true);
			$order = $transactionEntity->getOrderPayment()->getOrder();
			$order->addRelatedObject($transactionEntity->getOrderPayment());
			$order->save();
		}
	}
}