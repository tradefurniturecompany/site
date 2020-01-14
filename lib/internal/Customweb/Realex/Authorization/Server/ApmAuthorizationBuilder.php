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
 * This class creates the XML to check if a card is enrolled for 3-D secure.
 * 
 * @author Mathis Kappeler
 *
 */
final class Customweb_Realex_Authorization_Server_ApmAuthorizationBuilder extends Customweb_Realex_Authorization_AbstractXmlBuilder {
	
	protected function getCvc() {
		return null;
	}
	
	public function buildXml() {
		$requestType = Customweb_Realex_Method_Factory::getMethod($this->getTransaction()->getPaymentMethod(), $this->getConfiguration(), $this->container)->getRequestType();
		
		$payerId = $this->getTransaction()->getPayerId();
		if(isset($payerId)){
			$element = $this->getXMLHeader("payment-do");
		}else{
			$element = $this->getXMLHeader("payment-set");
		}
			$element = $element . $this->getBasicElements() .
			$this->getAuthorizationAmountElement().
			$this->getAutoSettleElement() .
			$this->getOrderIdElement() .
			$this->getPaymentMethodDetailsElement() .
			$this->getHashElement() .
			$this->getXMLFooter();
			
		return $element;
	}
}