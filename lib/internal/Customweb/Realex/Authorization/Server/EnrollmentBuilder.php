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
final class Customweb_Realex_Authorization_Server_EnrollmentBuilder extends Customweb_Realex_Authorization_AbstractXmlBuilder {
	
	protected function getCvc() {
		return null;
	}
	
	public function buildXml() {
		
		return $this->getXMLHeader("3ds-verifyenrolled") .
			$this->getBasicElements() .
			$this->getOrderIdElement() .
			$this->getAuthorizationAmountElement().
			$this->getAutoSettleElement() .
			$this->getPaymentMethodDetailsElement() .
			//TODO TSS info
			$this->getHashElement() .
			$this->getXMLFooter();
	}
}