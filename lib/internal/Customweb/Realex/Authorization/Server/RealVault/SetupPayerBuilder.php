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
 * This class is responsible for creating the XML file to setup
 * a payer over the remote interface for the RealVault.
 * 
 * @author Mathis Kappeler
 *
 */
final class Customweb_Realex_Authorization_Server_RealVault_SetupPayerBuilder extends Customweb_Realex_Authorization_AbstractXmlBuilder{
	
	public function buildXml(){
		return $this->getXMLHeader('payer-new') .
			$this->getBasicElements(false) .
			$this->getOrderIdElement() .
			$this->getPayerElement() .
			$this->getHashElement() .
			$this->getXMLFooter();
	}
	
	protected function getParametersToHash() {
		return array(
			$this->getTimestamp(),
			$this->getConfiguration()->getMerchantId(),
			$this->getTransaction()->getFormattedTransactionId($this->getConfiguration()),
			'',
			'',
			$this->getTransaction()->getPMRef(),
		);
	}
	
	protected function getCvc() {
		return null;
	}
	
	
	private function getPayerElement(){
		$payerRef = $this->getTransaction()->getPMRef();
		$xml = '<payer type="Business" ref="' . $payerRef . '">' .
			'<firstname>' . $this->getOrderContext()->getBillingFirstName() . '</firstname>' .
			'<surname>' . $this->getOrderContext()->getBillingLastName() . '</surname>' .
			'</payer>';
		return $xml;
	}
	
	
}

