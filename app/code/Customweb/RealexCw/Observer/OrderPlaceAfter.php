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

namespace Customweb\RealexCw\Observer;

use Magento\Framework\Event\ObserverInterface;

class OrderPlaceAfter implements ObserverInterface
{
	/**
	 * Core registry
	 *
	 * @var \Magento\Framework\Registry
	 */
	protected $_coreRegistry = null;

	/**
	 * @var \Customweb\RealexCw\Model\Authorization\TransactionFactory
	 */
	protected $_transactionFactory;

	/**
	 * @param \Magento\Framework\Registry $coreRegistry
	 * @param \Customweb\RealexCw\Model\Authorization\TransactionFactory $transactionFactory
	 */
	public function __construct(
			\Magento\Framework\Registry $coreRegistry,
			\Customweb\RealexCw\Model\Authorization\TransactionFactory $transactionFactory
	) {
		$this->_coreRegistry = $coreRegistry;
		$this->_transactionFactory = $transactionFactory;
	}

	public function execute(\Magento\Framework\Event\Observer $observer)
	{
		/* @var $order \Magento\Sales\Model\Order */
		$order = $observer->getEvent()->getOrder();
		if ($order->getPayment() instanceof \Magento\Sales\Model\Order\Payment
	    	&& $order->getPayment()->getMethodInstance() instanceof \Customweb\RealexCw\Model\Payment\Method\AbstractMethod) {
			$transaction = $this->_transactionFactory->create();
			$transaction->setOrder($order);
			$order->addRelatedObject($transaction);

			$this->_coreRegistry->register('realexcw_transaction', $transaction);
	    }
	}
}