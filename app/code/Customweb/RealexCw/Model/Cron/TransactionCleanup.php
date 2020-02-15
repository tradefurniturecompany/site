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

namespace Customweb\RealexCw\Model\Cron;

class TransactionCleanup
{
	const FAILED_TRANSACTION_TIMEOUT = 2;

	const PENDING_TRANSACTION_TIMEOUT = 6;

	const NOSTATUS_TRANSACTION_TIMEOUT = 1;

	/**
	 * Transaction collection factory
	 *
	 * @var \Customweb\RealexCw\Model\ResourceModel\Authorization\Transaction\CollectionFactory
	 */
	protected $_transactionCollectionFactory;

	/**
	 * @var \Psr\Log\LoggerInterface
	 */
	protected $_logger;

	/**
	 * @param \Customweb\RealexCw\Model\ResourceModel\Authorization\Transaction\CollectionFactory $transactionCollectionFactory
	 * @param \Psr\Log\LoggerInterface $logger
	 */
	public function __construct(
			\Customweb\RealexCw\Model\ResourceModel\Authorization\Transaction\CollectionFactory $transactionCollectionFactory,
			\Psr\Log\LoggerInterface $logger
	) {
		$this->_transactionCollectionFactory = $transactionCollectionFactory;
		$this->_logger = $logger;
	}

	public function execute()
	{
		$maxEndtime = \Customweb_Core_Util_System::getScriptExecutionEndTime() - 4;
		/** @var int $c */
		// Remove all failed transactions after 2 months.
		try {
			/**
			 * 2020-02-15 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
			 * "Prevent Customweb_RealexCw from logging «RealexCw: Cleaned up 0 failed transactions»":
			 * https://github.com/tradefurniturecompany/site/issues/37
			 */
			if ($c = $this->delete($this->getFailedTransactions(), $maxEndtime)) {
				$this->_logger->info("RealexCw: Cleaned up $c failed transactions.");
			}
		} catch (\Exception $e) {
			$this->_logger->error('Error in RealexCw transaction cleanup cron: ' . $e->getMessage());
		}

		//Remove all pending transaction 6 month after last update
		try {
			/**
			 * 2020-02-15 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
			 * "Prevent Customweb_RealexCw from logging «RealexCw: Cleaned up 0 pending transactions»":
			 * https://github.com/tradefurniturecompany/site/issues/38
			 */
			if ($c = $this->delete($this->getPendingTransactions(), $maxEndtime)) {
				$this->_logger->info("RealexCw: Cleaned up $c pending transactions.");
			}
		} catch (\Exception $e) {
			$this->_logger->error('Error in RealexCw transaction cleanup cron: ' . $e->getMessage());
		}

		//Remove all transaction with no status after 1 month
		try {
			$this->_logger->info('RealexCw: Cleaned up ' . $this->delete($this->getNoStatusTransactions(), $maxEndtime) . ' transactions with no status.');
		} catch (\Exception $e) {
			$this->_logger->error('Error in RealexCw transaction cleanup cron: ' . $e->getMessage());
		}
	}

	/**
	 * @param \Customweb\RealexCw\Model\Authorization\Transaction[] $items
	 * @param int $maxEndtime
	 * @return int
	 */
	private function delete($items, $maxEndtime)
	{
		$i = 0;
		foreach ($items as $item) {
			if ($maxEndtime > time()) {
				$item->delete();
				$i++;
			}
			else {
				return $i;
			}
		}
		return $i;
	}

	/**
	 * @return \Customweb\RealexCw\Model\Authorization\Transaction[]
	 */
	private function getFailedTransactions()
	{
		return $this->getCollection(self::FAILED_TRANSACTION_TIMEOUT, \Customweb_Payment_Authorization_ITransaction::AUTHORIZATION_STATUS_FAILED)->getItems();
	}

	/**
	 * @return \Customweb\RealexCw\Model\Authorization\Transaction[]
	 */
	private function getPendingTransactions()
	{
		return $this->getCollection(self::PENDING_TRANSACTION_TIMEOUT, \Customweb_Payment_Authorization_ITransaction::AUTHORIZATION_STATUS_PENDING)->getItems();
	}

	/**
	 * @return \Customweb\RealexCw\Model\Authorization\Transaction[]
	 */
	private function getNoStatusTransactions()
	{
		return $this->getCollection(self::NOSTATUS_TRANSACTION_TIMEOUT, '')->getItems();
	}

	/**
	 * @return \Customweb\RealexCw\Model\ResourceModel\Authorization\Transaction\Collection
	 */
	private function getCollection($timeout, $authorizationStatus)
	{
		$collection = $this->_transactionCollectionFactory->create();
		$collection->setCurPage(1)->setPageSize(40);
		$collection->addFieldToFilter('updated_at', ['to' => new \Zend_Db_Expr('NOW() - INTERVAL ' . (int)$timeout . ' MONTH')]);
		$collection->addFieldToFilter('authorization_status', ['eq' => $authorizationStatus]);
		return $collection;
	}
}