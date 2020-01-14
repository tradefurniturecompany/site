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
 *
 */
class Customweb_Realex_BackendOperation_Adapter_CaptureXmlBuilder extends Customweb_Realex_BackendOperation_AbstractXmlBuilder {
	private $amount = null;
	private $close = false;
	
	public function __construct(Customweb_Payment_Authorization_ITransaction $transaction, Customweb_Realex_Configuration $configuration, $amount, Customweb_DependencyInjection_IContainer $container) {
		//parent::__construct($transaction, $configuration, array(), $this->getPaymentMethod(), $container);
		parent::__construct($transaction, $configuration, $container);
		
		$this->amount = $amount;
	}
	
	public function buildXml() {
		return $this->getXMLHeader('settle') .
		$this->getBasicElements() .
		$this->getOrderIdElement() .
		$this->getAmountElement($this->amount) .
		$this->getPasrefElement() .
		$this->getAuthcodeElement() .
		$this->getHashElement() .
		$this->getXMLFooter();
	}
	
	protected function getParametersToHash() {
		return array(
			$this->getTimestamp(),
			$this->getConfiguration()->getMerchantId(),
			$this->getTransaction()->getFormattedTransactionId($this->getConfiguration()),
			Customweb_Realex_Util::formatAmount($this->amount, $this->getTransaction()->getCurrencyCode()),
			$this->getTransaction()->getCurrencyCode(),
			'',
		);
	}
}