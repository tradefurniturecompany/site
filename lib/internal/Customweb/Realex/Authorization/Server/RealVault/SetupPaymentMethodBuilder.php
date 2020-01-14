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
 * This class creates the XML to set up a payment method at the RealVault. 
 * 
 * @author Mathis Kappeler
 *
 */
final class Customweb_Realex_Authorization_Server_RealVault_SetupPaymentMethodBuilder extends Customweb_Realex_Authorization_AbstractXmlBuilder{
	
	public function buildXml() {
		return $this->getXMLHeader('card-new') .
			$this->getBasicElements(false) .
			$this->getOrderIdElement() .
			$this->getCardDataElement() .
			$this->getHashElement() .
			$this->getXMLFooter();
	}
	
	protected function getCvc() {
		return null;
	}
	
	protected function getCardDataElement() {
				
		$payerref = 
			"<ref>" . $this->getTransaction()->getPMRef()  . "</ref>" .
			"<payerref>" . $this->getTransaction()->getPMRef() . "</payerref>";
		
		$card = Customweb_Realex_Method_Factory::getMethod($this->getTransaction()->getPaymentMethod(), $this->getConfiguration(), $this->container)->getPaymentMethodDetailsElement($this->getTransaction(), $payerref);
		
		return $card;
	}
	
	protected function getParametersToHash() {
		$pmp = Customweb_Realex_Method_Factory::getMethod($this->getTransaction()->getPaymentMethod(), $this->getConfiguration(), $this->container)->getPaymentMethodHashParam($this->getTransaction());
		
		 $param = array(
			$this->getTimestamp(),
			$this->getConfiguration()->getMerchantId(),
			$this->getTransaction()->getFormattedTransactionId($this->getConfiguration()),
			'',
			'',
			$this->getTransaction()->getPMRef(),
			$this->getTransaction()->getCardHolderName(),
			$pmp,
		);
		return $param;
	}
	
}

