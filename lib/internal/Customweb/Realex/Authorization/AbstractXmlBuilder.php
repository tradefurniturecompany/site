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
 * This class provides common methods to build XMLs for the authorization process. 
 * 
 * @author Mathis Kappeler
 *
 */
abstract class Customweb_Realex_Authorization_AbstractXmlBuilder extends Customweb_Realex_Xml_AbstractBuilder {

	/**
	 * Returns the amount elment for the authorization amount of the transaction.
	 * 
	 * @return string
	 */
	protected function getAuthorizationAmountElement(){
		return $this->getAmountElement($this->getOrderContext()->getOrderAmountInDecimals());
	}
	
	/**
	 * Returns the formatted authorization amount.
	 *
	 * @return string
	 */
	protected function getFormattedAuthorizationAmount() {
		$amount = Customweb_Realex_Util::formatAmount(
			$this->getOrderContext()->getOrderAmountInDecimals(),
			$this->getOrderContext()->getCurrencyCode()
		);
	}
	
	/**
	 * This method returns either the CVC or null. In case the method returns null,
	 * the transaction will be threaded as a CVC-not-present transaction.
	 * 
	 * @return null | string
	 */	
	protected function getCvc() {
		$cvc = $this->getTransaction()->getCvc();
		if (isset($cvc) && $cvc !== null) {
			return preg_replace('/[^0-9]+/', '', $cvc);
		}
		else {
			return null;
		}
	}
		
	/**
	 * Returns the card holder name.
	 * 
	 * @return string
	 */
	protected function getCardHolderName() {
		return $this->getTransaction()->getCardHolderName();
	}
	
	/**
	 * This method returns true, when the CVC is present. Otherwise it 
	 * returns false.
	 * 
	 * @return boolean
	 */
	protected function isCvcPresent($transaction) {
		if ($transaction->getCvc() === null || strlen($transaction->getCvc()) < 3) {
			return false;
		}
		else {
			return true;
		}
	}
	
	protected function getCvcElement($transaction){
		$cvn = '';
		if ($this->isCvcPresent($transaction)) {
			$cvn = $transaction->getCvc();
		}
		
		$xml = "<cvn><number>" . $cvn . "</number>";
		if($this->isCvcPresent($transaction)){
			$xml .= "<presind>1</presind>";
		}else{
			$xml .= "<presind>4</presind>";
		}
		
		return $xml . "</cvn>";
	}

	protected function getParametersToHash(){
		$pmp = Customweb_Realex_Method_Factory::getMethod($this->getTransaction()->getPaymentMethod(), $this->getConfiguration(), $this->container)->getPaymentMethodHashParam($this->getTransaction());
		
		$param = array(
			$this->getTimestamp(),
			$this->getConfiguration()->getMerchantId(),
			$this->getTransaction()->getFormattedTransactionId($this->getConfiguration()),
			Customweb_Realex_Util::formatAmount(
				$this->getOrderContext()->getOrderAmountInDecimals(),
				$this->getOrderContext()->getCurrencyCode()),
			$this->getOrderContext()->getCurrencyCode(),
			$pmp,
		);
		
		return $param;
	}
	
	
	protected function getTSSInfoElement(){
		//AVS parameters if AVS is activated by shopsetting
		$AVSCode = '';
		if($this->getConfiguration()->isAVSActive()){
			$AVSCode = Customweb_Realex_Util::getAVSParameterValue($this->getOrderContext()->getBillingPostCode(), $this->getOrderContext()->getBillingStreet(), $this->getOrderContext()->getBillingAddress()->getCountryIsoCode());
			if($AVSCode){
				$AVSCode = "<code>" . $AVSCode . "</code>";
			}
		}
		return
		
		"<tssinfo>
			<address type='billing'>
				<country>" . $this->getOrderContext()->getBillingCountryIsoCode() . "</country>"
				. $AVSCode .
			"</address>
			<address type='shipping'>
				<country>" . $this->getOrderContext()->getShippingCountryIsoCode() . "</country>
			</address>
		</tssinfo>";
	}
	
	
	protected function getAutoSettleElement(){
		if ($this->getTransaction()->isCaptureDeferred()) {
			$flag = '0';
		}
		else {
			$flag = '1';
		}
		return "<autosettle flag='" . $flag . "'> </autosettle>";
	}
	
	
	protected function getPaymentMethodDetailsElement(){
		return Customweb_Realex_Method_Factory::getMethod($this->getTransaction()->getPaymentMethod(), $this->getConfiguration(), $this->container)->getPaymentMethodDetailsElement($this->getTransaction());
	}
}