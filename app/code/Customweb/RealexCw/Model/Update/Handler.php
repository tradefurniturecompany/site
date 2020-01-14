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

namespace Customweb\RealexCw\Model\Update;

class Handler implements \Customweb_Payment_Update_IHandler
{
	const MAX_NUMBER_OF_TRANSACTIONS = 100;

	/**
	 * @var \Customweb\RealexCw\Model\DependencyContainer
	 */
	protected $_container;

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

	/**
	 * @param \Customweb\RealexCw\Model\DependencyContainer $container
	 * @return \Customweb\RealexCw\Model\Update\Handler
	 */
	public function setContainer(\Customweb\RealexCw\Model\DependencyContainer $container)
	{
		$this->_container = $container;
		return $this;
	}

	public function getContainer()
	{
		return $this->_container;
	}

	public function log($message, $type)
	{
		if ($type == \Customweb_Payment_Update_IHandler::LOG_TYPE_ERROR) {
			$this->_logger->error($message);
		} else {
			$this->_logger->info($message);
		}
	}

	public function getScheduledTransactionIds()
	{
		return $this->_transactionCollectionFactory->create()->setUpdateFilter(self::MAX_NUMBER_OF_TRANSACTIONS)->getAllIds();
	}

	/**
	 * @return \Customweb\RealexCw\Model\Authorization\Transaction
	 */
	private function createEntity() {
		return $this->_transactionFactory->create();
	}
}