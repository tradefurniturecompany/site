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
 */



/**
 *
 * @author Mathis Kappeler
 * @Bean
 */
class Customweb_Realex_BackendOperation_Adapter_RefundAdapter extends Customweb_Realex_AbstractAdapter implements Customweb_Payment_BackendOperation_Adapter_Service_IRefund {

	public function refund(Customweb_Payment_Authorization_ITransaction $transaction){
		if (!($transaction instanceof Customweb_Realex_Authorization_Transaction)) {
			throw new Exception("The given transaction is not of type Customweb_Realex_Authorization_Transaction.");
		}
		$items = $transaction->getNonRefundedLineItems();
		$this->partialRefund($transaction, $items, true);
	}

	public function partialRefund(Customweb_Payment_Authorization_ITransaction $transaction, $items, $close){
		if (!($transaction instanceof Customweb_Realex_Authorization_Transaction)) {
			throw new Exception("The given transaction is not of type Customweb_Realex_Authorization_Transaction.");
		}
		$transaction->refundByLineItemsDry($items, $close);

		$amount = Customweb_Util_Invoice::getTotalAmountIncludingTax($items);
		$processor = new Customweb_Realex_BackendOperation_XmlProcessor(
			$this->getConfiguration(),
			new Customweb_Realex_BackendOperation_Adapter_RefundXmlBuilder($transaction, $this->getConfiguration(), $amount, $this->getContainer())
				,$transaction,
				$this->getContainer()
		);

		try {
			$processor->process();
			$transaction->refundByLineItems($items, $close);
		} catch (Exception $e) {
			throw new Exception(Customweb_I18n_Translation::__('Refund failed with message: !message', array('!message' => $e->getMessage())));
		}
	}
}