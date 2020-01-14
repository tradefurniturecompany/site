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

class Notification
{
	/**
	 * Core store config
	 *
	 * @var \Magento\Framework\App\Config\ScopeConfigInterface
	 */
	protected $_scopeConfig;

	/**
	 * @var \Customweb\RealexCw\Model\Authorization\TransactionFactory
	 */
	protected $_transactionFactory;

	/**
	 * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
	 * @param \Customweb\RealexCw\Model\Authorization\TransactionFactory $transactionFactory
	 */
	public function __construct(
			\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
			\Customweb\RealexCw\Model\Authorization\TransactionFactory $transactionFactory
	) {
		$this->_scopeConfig = $scopeConfig;
		$this->_transactionFactory = $transactionFactory;
	}

	/**
	 * @param int $transactionId
	 * @param string $timeoutErrorMessage
	 * @throws \Exception
	 */
	public function waitForNotification($transactionId, $timeoutErrorMessage = null)
	{
		if ($this->_scopeConfig->getValue('realexcw/shop/await_notification') == '0') {
			return;
		}

		$maxTime = min([\Customweb_Util_System::getMaxExecutionTime() - 4, 30]);
		$startTime = microtime(true);
		while(true){
			if (microtime(true) - $startTime >= $maxTime) {
				break;
			}

			/* @var $transaction \Customweb\RealexCw\Model\Authorization\Transaction */
			$transaction = $this->_transactionFactory->create()->load($transactionId);
			if ($transaction == null || !$transaction->getId() || $transaction->getTransactionObject() == null) {
				continue;
			}
			if ($transaction->getTransactionObject()->isAuthorized()) {
				return;
			}
			if ($transaction->getTransactionObject()->isAuthorizationFailed()) {
				throw new \Exception($transaction->getLastErrorMessage());
			}

			sleep(1);
		}

		if (empty($timeoutErrorMessage)) {
			$timeoutErrorMessage = 'There has been a problem during the processing of your payment. Please contact the shop owner to make sure your order was placed successfully.';
		}
		throw new \Exception(__($timeoutErrorMessage));
	}
}