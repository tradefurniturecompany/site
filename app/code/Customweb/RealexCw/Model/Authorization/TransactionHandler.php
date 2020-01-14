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

class TransactionHandler implements \Customweb_Payment_ITransactionHandler
{
	const TRANSACTION_ID_FIELD_KEY = 'entity_id';
	const PAYMENT_ID_FIELD_KEY = 'payment_id';
	const TRANSACTION_EXTERNAL_ID_FIELD_KEY = 'transaction_external_id';
	const ORDER_ID_FIELD_KEY = 'order_id';

	/**
	 * @var array
	 */
	private $cache = [];

	private $internalHandler = null;

	/**
	 * @var array
	 */
	private $cacheMaps = [
		self::PAYMENT_ID_FIELD_KEY => [],
		self::TRANSACTION_EXTERNAL_ID_FIELD_KEY => [],
		self::ORDER_ID_FIELD_KEY => []
	];

	/**
	 * Transaction model factory
	 *
	 * @var \Customweb\RealexCw\Model\Authorization\TransactionFactory
	 */
	protected $_transactionFactory;

	/**
	 * @var \Customweb\RealexCw\Model\Configuration
	 */
	protected $_configuration;

	public function __construct(
			\Customweb\RealexCw\Model\Authorization\TransactionFactory $transactionFactory,
			\Customweb\RealexCw\Model\Configuration $configuration
	) {
		$this->_transactionFactory = $transactionFactory;
		$this->_configuration = $configuration;
	}

	public function isTransactionRunning()
	{
		return false;
	}

	public function beginTransaction()
	{
		$this->getInternalHandler()->beginTransaction();
	}

	public function commitTransaction()
	{
		$this->getInternalHandler()->commit();
	}

	public function rollbackTransaction()
	{
		$this->getInternalHandler()->rollBack();
	}

	public function findTransactionByTransactionExternalId($transactionId, $useCache = true)
	{
		$transaction = $this->loadTransaction($transactionId, self::TRANSACTION_EXTERNAL_ID_FIELD_KEY, $useCache);
		if ($transaction !== null) {
			return $transaction->getTransactionObject();
		} else {
			throw new \Exception("The transaction could not be loaded by transaction external id.");
		}
	}

	public function findTransactionByPaymentId($paymentId, $useCache = true)
	{
		$transaction = $this->loadTransaction($paymentId, self::PAYMENT_ID_FIELD_KEY, $useCache);
		if ($transaction !== null) {
			return $transaction->getTransactionObject();
		} else {
			throw new \Exception("The transaction could not be loaded by payment id.");
		}
	}

	public function findTransactionByTransactionId($transactionId, $useCache = true)
	{
		$transaction = $this->loadTransaction($transactionId, self::TRANSACTION_ID_FIELD_KEY, $useCache);
		if ($transaction !== null) {
			return $transaction->getTransactionObject();
		} else {
			throw new \Exception("The transaction could not be loaded by transaction id.");
		}
	}

	public function findTransactionsByOrderId($orderId, $useCache = true)
	{
		$transaction = $this->loadTransaction($orderId, self::ORDER_ID_FIELD_KEY, $useCache);
		if ($transaction != null) {
			return [$transaction->getTransactionObject()];
		} else {
			return [];
		}
	}

	public function persistTransactionObject(\Customweb_Payment_Authorization_ITransaction $transaction)
	{
		try {
			$this->createEntity()->loadByTransactionExternalId($transaction->getExternalTransactionId())->setTransactionObject($transaction)->save();
		} catch (\Customweb\RealexCw\Model\Exception\OptimisticLockingException $e) {
			throw new \Customweb_Payment_Exception_OptimisticLockingException($transaction->getTransactionId());
		}
	}

	/**
	 *
	 *
	 * @param mixed $value
	 * @param string $field
	 * @param boolean $useCache
	 * @return \Customweb\RealexCw\Model\Authorization\Transaction
	 */
	private function loadTransaction($value, $field, $useCache)
	{
		if ($useCache) {
			$transaction = $this->loadFromCache($value, $field);
			if ($transaction !== null) {
				$this->_configuration->setStore($transaction->getStore());
				return $transaction;
			}
		}
		$transaction = $this->createEntity()->load($value, $field);
		if (!$transaction->getId()) {
			return null;
		}
		$this->storeInCache($transaction);
		$this->_configuration->setStore($transaction->getStore());
		return $transaction;
	}

	/**
	 * Load the transaction from the cache or return null.
	 *
	 * @param mixed $value
	 * @param string $field
	 * @return null|\Customweb\RealexCw\Model\Authorization\Transaction
	 */
	private function loadFromCache($value, $field = null)
	{
		if ($field != null) {
			if (array_key_exists($field, $this->cacheMaps) && array_key_exists($value, $this->cacheMaps[$field])) {
				$value = $this->cacheMaps[$field][$value];
			} else {
				return null;
			}
		}
		if (array_key_exists($value, $this->cache)) {
			return $this->cache[$value];
		}
		return null;
	}

	/**
	 * Store the transaction in the cache.
	 *
	 * @param \Customweb\RealexCw\Model\Authorization\Transaction $transaction
	 */
	private function storeInCache(\Customweb\RealexCw\Model\Authorization\Transaction $transaction)
	{
		$this->cache[$transaction->getId()] = $transaction;
		$this->cacheMaps[self::PAYMENT_ID_FIELD_KEY][$transaction->getPaymentId()] = $transaction->getId();
		$this->cacheMaps[self::TRANSACTION_EXTERNAL_ID_FIELD_KEY][$transaction->getTransactionExternalId()] = $transaction->getId();
		$this->cacheMaps[self::ORDER_ID_FIELD_KEY][$transaction->getOrderId()] = $transaction->getId();
	}

	/**
	 * @return \Customweb\RealexCw\Model\Authorization\Transaction
	 */
	private function createEntity() {
		return $this->_transactionFactory->create();
	}

	private function getInternalHandler(){
		if($this->internalHandler === null){
			$this->internalHandler = $this->createEntity()->getResource();
		}
		return $this->internalHandler;
	}
}