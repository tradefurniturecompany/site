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
 * Deticated implementation of teh XML builder. The main difference to
 * a regular authentication builder is to have different channel and
 * a may be a different merchant id.
 * 
 * @author Mathis Kappeler
 *
 */
final class Customweb_Realex_Authorization_Moto_AuthorizationBuilder extends Customweb_Realex_Authorization_AbstractRemoteXmlBuilder {
	
	
	public function buildXml() {
		return $this->getXMLHeader('auth') .
			$this->getBasicElements() .
			$this->getOrderIdElement() .
			$this->getAuthorizationAmountElement() .
			$this->getChannel() .
			$this->getPaymentMethodDetailsElement() .
			$this->getAutoSettleElement() .
			$this->getReccuringElement() . 
			$this->getMpiOrTssInfoElement() .
			$this->getHashElement() .
			$this->getXMLFooter();
	}
	
	protected function getMerchantid(){
		$motoMerchantId = $this->getConfiguration()->getMotoMerchantId();
		if(isset($motoMerchantId) && !empty($motoMerchantId)){
			return $motoMerchantId;
		}
		return $this->getConfiguration()->getMerchantId();
	}
	
	/**
	 * According to the documentation the MOTO-field can also be ECOM. This depends on the Acquire.
	 */
	protected function getChannel() {
		return '<channel>MOTO</channel>';
	}
}

