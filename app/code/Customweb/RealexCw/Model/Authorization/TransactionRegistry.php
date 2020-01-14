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

namespace Customweb\RealexCw\Model\Authorization;

class TransactionRegistry
{
	/**
	 * @var array
	 */
	protected $registryTransactionId = [];

	/**
	 * @var array
	 */
	protected $registryPaymentId = [];

	/**
	 * @var array
	 */
	protected $registryOrderId = [];

	/**
	 * @var TransactionFactory
	 */
	protected $_transactionFactory;

	/**
	 * @param TransactionFactory $groupFactory
	 */
	public function __construct(
			TransactionFactory $transactionFactory
	) {
		$this->_transactionFactory = $transactionFactory;
	}

	/**
	 * Get instance of the Transaction Model identified by an id
	 *
	 * @param int $transactionId
	 * @return Transaction
	 * @throws \Magento\Framework\Exception\NoSuchEntityException
	 */
	public function retrieve($transactionId)
	{
		if (isset($this->registryTransactionId[$transactionId])) {
			return $this->registryTransactionId[$transactionId];
		}
		$transaction = $this->_transactionFactory->create();
		$transaction->load($transactionId);
		if ($transaction->getId() === null || $transaction->getId() != $transactionId) {
			throw \Magento\Framework\Exception\NoSuchEntityException::singleField(\Customweb\RealexCw\Api\Data\TransactionInterface::ENTITY_ID, $transactionId);
		}

		return $transaction;
	}

	/**
	 * Get instance of the Transaction Model identified by a payment id
	 *
	 * @param string $paymentId
	 * @return Transaction
	 * @throws \Magento\Framework\Exception\NoSuchEntityException
	 */
	public function retrieveByPaymentId($paymentId)
	{
		if (isset($this->registryPaymentId[$paymentId])) {
			return $this->registryPaymentId[$paymentId];
		}
		$transaction = $this->_transactionFactory->create();
		$transaction->loadByPaymentId($paymentId);
		if ($transaction->getId() === null || $transaction->getPaymentId() != $paymentId) {
			throw \Magento\Framework\Exception\NoSuchEntityException::singleField(\Customweb\RealexCw\Api\Data\TransactionInterface::ENTITY_ID, $paymentId);
		}
		$this->registryPaymentId[$paymentId] = $transaction;
		return $transaction;
	}

	/**
	 * Get instance of the Transaction Model identified by an order id
	 *
	 * @param int $orderId
	 * @return Transaction
	 * @throws \Magento\Framework\Exception\NoSuchEntityException
	 */
	public function retrieveByOrderId($orderId)
	{
		if (isset($this->registryOrderId[$orderId])) {
			return $this->registryOrderId[$orderId];
		}
		$transaction = $this->_transactionFactory->create();
		$transaction->loadByOrderId($orderId);
		if ($transaction->getId() === null || $transaction->getOrderId() != $orderId) {
			throw \Magento\Framework\Exception\NoSuchEntityException::singleField(\Customweb\RealexCw\Api\Data\TransactionInterface::ENTITY_ID, $orderId);
		}
		$this->registryOrderId[$orderId] = $transaction;
		return $transaction;
	}

	/**
	 * Remove an instance of the Transaction Model from the registry
	 *
	 * @param Transaction $transaction
	 * @return void
	 */
	public function remove($transaction)
	{
		unset($this->registryTransactionId[$transaction->getId()]);
		unset($this->registryPaymentId[$transaction->getPaymentId()]);
		unset($this->registryOrderId[$transaction->getOrderId()]);
	}

	/**
	 * @param Transaction $transaction
	 */
	private function registerTransaction(Transaction $transaction)
	{
		$this->registryTransactionId[$transaction->getId()] = $transaction;
		$this->registryPaymentId[$transaction->getPaymentId()] = $transaction;
		$this->registryOrderId[$transaction->getOrderId()] = $transaction;
	}
}