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
 * This class creates the XML to authorize a transaction over the remote interface.
 * 
 * @author Mathis Kappeler
 *
 */
final class Customweb_Realex_Authorization_Server_AuthorizationBuilder extends Customweb_Realex_Authorization_AbstractRemoteXmlBuilder {
	
	public function buildXml() {
		$authType = Customweb_Realex_Method_Factory::getMethod($this->getTransaction()->getPaymentMethod(), $this->getConfiguration(), $this->container)->getPaymentMethodAuthType(); 
		
		return $this->getXMLHeader($authType) .
			$this->getBasicElements() .
			$this->getOrderIdElement() .
			$this->getAuthorizationAmountElement() .
			$this->getPaymentMethodDetailsElement() .
			$this->getAutoSettleElement() .
			$this->getReccuringElement() . 
			$this->getMpiOrTssInfoElement() .
			$this->getHashElement() .
			$this->getXMLFooter();
	}
}

