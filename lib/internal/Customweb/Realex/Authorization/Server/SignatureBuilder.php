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
 * This class builds the XML to verify if the given response from the 3-D secure challenge
 * is valid.
 * 
 * @author Mathis Kappeler
 *
 */
final class Customweb_Realex_Authorization_Server_SignatureBuilder extends Customweb_Realex_Authorization_AbstractXmlBuilder {
	
	private $pares = null;
	
	public function __construct(Customweb_Realex_Authorization_Transaction $transaction, Customweb_Realex_Configuration $configuration, $pares, Customweb_DependencyInjection_IContainer $container) {
		parent::__construct($transaction, $configuration, $container);
		$this->pares = $pares;
	}
	
	public function buildXml() {
		return $this->getXMLHeader('3ds-verifysig') .
			$this->getBasicElements() .
			$this->getOrderIdElement() .
			$this->getAuthorizationAmountElement() .
			$this->getPaymentMethodDetailsElement() .
			$this->getParesElement() .
			$this->getHashElement() .
			$this->getXMLFooter();
	}
	
	protected function getCvc() {
		return null;
	}
	
	private function getParesElement() {
		return "<pares>" . $this->pares . "</pares>";
	}
}

