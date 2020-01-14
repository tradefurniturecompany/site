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
use Magento\Sales\Model\Order;
use Customweb\RealexCw\Model\Payment\Method\AbstractMethod;

class CancelPayment implements ObserverInterface
{
	/**
     * @var \Magento\Checkout\Model\Session
     */
	protected $_checkoutSession;

	/**
	 * @var \Customweb\RealexCw\Model\Authorization\TransactionFactory
	 */
	protected $_transactionFactory;

	/**
	 * @param \Magento\Checkout\Model\Session $checkoutSession
	 * @param \Customweb\RealexCw\Model\Authorization\TransactionFactory $transactionFactory
	 */
	public function __construct(
			\Magento\Checkout\Model\Session $checkoutSession,
			\Customweb\RealexCw\Model\Authorization\TransactionFactory $transactionFactory
	) {
		$this->_checkoutSession = $checkoutSession;
		$this->_transactionFactory = $transactionFactory;
	}

	public function execute(\Magento\Framework\Event\Observer $observer)
	{
		$order = $this->_checkoutSession->getLastRealOrder();
		if ($order instanceof Order && $order->getId()) {
			if ($order->getState() === Order::STATE_PENDING_PAYMENT) {
				if ($order->getPayment() instanceof AbstractMethod) {
					$order->registerCancellation('')->save();
					$this->_checkoutSession->restoreQuote();
				}
			}
		}
	}
}