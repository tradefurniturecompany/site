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

namespace Customweb\RealexCw\Controller\Checkout;

class Error extends \Customweb\RealexCw\Controller\Checkout
{
	public function execute()
	{
		/* @var $transaction \Customweb\RealexCw\Model\Authorization\Transaction */
		$transactionId = $this->getRequest()->getParam('transaction_id');
		if (!empty($transactionId)) {
			$transaction = $this->getTransaction($transactionId);
			if ($transaction->getOrder() instanceof \Magento\Sales\Model\Order) {
				if ($transaction->getOrder()->canCancel()) {
					$transaction->getOrder()->cancel()->save();
				}
			}
		}

		$this->messageManager->addError(__('Please flush or disable the cache storage and retry. If this did not help, change the authorization method to PaymentPage and <a href="%s" target="_blank">contact sellxed</a>.', 'http://www.sellxed.com/en/support'));
		return $this->resultRedirectFactory->create()->setPath('checkout/onepage/index', ['_secure' => true]);
	}
}