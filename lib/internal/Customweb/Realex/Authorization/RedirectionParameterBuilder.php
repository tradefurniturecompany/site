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
 * This Class genereates the parameters used for the redirection authorization methods.
 *
 * @author Mathis Kappeler
 *
 */
final class Customweb_Realex_Authorization_RedirectionParameterBuilder extends Customweb_Realex_AbstractParameterBuilder {

	/**
	 * Builds the request array which is sent to the Paymentpage by post
	 * 
	 * @param array $formData
	 * @return array $parameters
	 */
	public function buildParameters() {
		$parameters = array_merge(
			$this->getBasicParameters(),
			$this->getTSSParameters(),
			$this->getAmountParameters()
		);
		
		
		$lang = $this->getTransaction()->getTransactionContext()->getOrderContext()->getLanguage();
		$supportedLanguages = array ('de_DE', 'es_ES', 'en_US');
		$language = Customweb_Payment_Util::getCleanLanguageCode($lang, $supportedLanguages);
		if($language == 'de_DE'){
			$parameters['HPP_LANG'] = 'ge'; //could also be ger
		}elseif($language == 'es_ES'){
			$parameters['HPP_LANG'] = 'sp';
		}else{
			$parameters['HPP_LANG'] = 'en';
		}
		
		$parameters['TIMESTAMP'] = strftime("%Y%m%d%H%M%S");
		$parameters['OFFER_SAVE_CARD'] = '0';
		$parameters['MERCHANT_RESPONSE_URL'] = $this->container->getBean('Customweb_Payment_Endpoint_IAdapter')->getUrl("process", "common", array('cw_transaction_id' => $this->getTransaction()->getExternalTransactionId()));

		
		if($this->getTransaction()->getTransactionContext()->getAlias() == 'new' || $this->getTransaction()->getTransactionContext()->createRecurringAlias()){
			$parameters['CARD_STORAGE_ENABLE'] = '1';
			
			$parameters['PAYER_REF'] = $this->getTransaction()->getPMRef();
			$parameters['PMT_REF'] = $this->getTransaction()->getPMRef();
			
			$parameters['PAYER_EXIST'] = '0';
			
		}
		$parameters['shop_id'] = $this->getConfiguration()->getShopId();
		
		if ($this->getTransaction()->isCaptureDeferred()) {
			$parameters['AUTO_SETTLE_FLAG'] = 0;
		}
		else {
			$parameters['AUTO_SETTLE_FLAG'] = 1;
		}
		
		$parameters['ACCOUNT'] = $this->getConfiguration()->getSubaccount();
		
		$hash = new Customweb_Realex_Hash_Hash(
			$this->getRequestStringToHash($parameters), 
			$this->getConfiguration()->getSignatureKey(), 
			$this->getConfiguration()->getEncriptionAlgorithm()
		);
		
		$parameters[$hash->getHashKeyUppercase()] = $hash->getHash(); 

		$this->sanatizeArray($parameters);
		
		return $parameters;
	}
	
	
	protected function getBasicParameters(){
		$parameters = array();
		$parameters['MERCHANT_ID'] = $this->getConfiguration()->getMerchantId();
		$parameters['ORDER_ID'] = $this->getTransaction()->getFormattedTransactionId($this->getConfiguration());
		//$parameters['cw_transaction_id'] =  $this->getTransaction()->getExternalTransactionId();
		
		return $parameters;
	}
	
	protected function getTSSParameters(){
		return array_merge(
				array('RETURN_TSS' => 1),
				$this->getShippingParameters(),
				$this->getBillingParameters());
	}
	
	protected function getShippingParameters(){
		$parameters = array();
		$parameters['SHIPPING_CODE'] = $this->getOrderContext()->getShippingPostCode();
		$parameters['SHIPPING_CO'] = $this->getOrderContext()->getShippingCountryIsoCode();
		return $parameters;
	}
	
	protected function getBillingParameters(){
		$parameters = array();
		if($this->getConfiguration()->isAVSActive()){
			$billingCode = Customweb_Realex_Util::getAVSParameterValue($this->getOrderContext()->getBillingAddress()->getPostCode(), $this->getOrderContext()->getBillingAddress()->getStreet(), $this->getOrderContext()->getBillingAddress()->getCountryIsoCode());
			if(!$billingCode) {
				$billingCode = $this->getOrderContext()->getBillingPostCode();
			}
		}else{
			$billingCode = $this->getOrderContext()->getBillingPostCode();
		}
		
		$parameters['BILLING_CODE'] = $billingCode;
		$parameters['BILLING_CO'] = $this->getOrderContext()->getBillingCountryIsoCode();
		return $parameters;
	}
	
	protected function getAmountParameters(){
		$parameters = array();
	
		$parameters['AMOUNT'] = Customweb_Realex_Util::formatAmount(
				$this->getOrderContext()->getOrderAmountInDecimals(),
				$this->getOrderContext()->getCurrencyCode());
		
		$parameters['MY_CURRENCY'] = $this->getOrderContext()->getCurrencyCode();
		$parameters['CURRENCY'] = $this->getOrderContext()->getCurrencyCode();
		return $parameters;
	}
	
	
	private function getRequestStringToHash($parameters){
		$hashElements = array(
				$parameters['TIMESTAMP'] ,
				$parameters['MERCHANT_ID'] ,
				$parameters['ORDER_ID'] ,
				$parameters['AMOUNT'] ,
				$parameters['CURRENCY']);
	
		if($this->getTransactionContext()->getAlias() != null || $this->getTransaction()->getTransactionContext()->createRecurringAlias()){
			//Concatenate timestamp, Merchant_id, orderid, amount, currencyCode, payer_ref, pmt_ref
			$hashElements[] = $parameters['PAYER_REF'];
			$hashElements[] = $parameters['PMT_REF'];
		}		
		return Customweb_Realex_Util::generateStringToHash($hashElements);
	}
	
	/**
	 * Cleans the parameter array according to the definition of Realex Payments
	 * 
	 * @param $parameters array
	 * @return modified parameter array
	 */
	private function sanatizeArray($parameters){
		$this->removeForbiddenCaracters($parameters);
		$this->checkSize($parameters);
	}
	
	
	/**
	 * Check and modifies the length of the parameter fields according to the Realex Payments manual
	 * 
	 * @param $parameters array
	 * @return modified parameter array
	 */
	private function checkSize($parameters){
		$lengthBoundArray = array(
				'MERCHANT_ID' 		=> array(1, 50),
				"ACCOUNT" 			=> array(0, 30),
				'ORDER_ID'			=> array(0, 40),
				'AMOUNT'			=> array(2, 11),
				'CURRENCY'			=> array(3, 3),
				'TIMESTAMP'			=> array(14, 14),
				'OFFER_SAVE_CARD'	=> array(1, 1),
				'PAYER_REF'			=> array(0, 50),
				'PMT_REF'			=> array(0, 30),
				'PAYER_EXIST'		=> array(1, 1),
				'RECURRING_TYPE'	=> array(14, 14),
				'RECURRING_SEQUENCE'=> array(14, 14),
				"MD5HASH"			=> array(32, 32),
				"SHA1HASH" 			=> array(40, 40),
				'AUTO_SETTLE_FLAG'	=> array(1, 1),
				"COMMENT1" 			=> array(0, 255),
				"COMMENT2" 			=> array(0, 255),
				"RETURN_TSS" 		=> array(1, 1),
				"SHIPPING_CODE" 	=> array(0, 30),
				"SHIPPING_CO" 		=> array(0, 30),
				"BILLING_CODE" 		=> array(0, 30),
				"BILLING_CO"		=> array(0, 30),
				"CUST_NUM" 			=> array(0, 50),
				"VAR_REF" 			=> array(0, 50),
				"PROD_ID" 			=> array(0, 50)
		);
		
		foreach ($parameters as $field => $value) {
			if(isset($lengthBoundArray[$field])){//known parameter
				//Cut parameter field if it is to long
				if(strlen($parameters[$field]) > $lengthBoundArray[$field][1]){
					$parameters[$field] = substr($parameters[$field], 0, $lengthBoundArray[$field][1]);
				}
			
				if(strlen($parameters[$field]) < $lengthBoundArray[$field][0]){ 
					throw new Exception(
						Customweb_I18n_Translation::__("The field '!field' is to short. It should be (at least) !length characters long. Please contact us to check why your order fails.", 
							array(
								'!field' => $field,
								'!length' => $lengthBoundArray[$field][0],
							))
					);
				}
			}else{//unknown parameter (anyting else)
				if(strlen($parameters[$field]) > 255){
					$parameters[$field] = substr($parameters[$field], 0, 255);
				}
			}
		}
	}
	
	/**
	 * Remove unallowed characters according to the Realex Payments manual
	 * 
	 * @param $parameters array
	 * @return modified parameter array
	 */
	private function removeForbiddenCaracters($parameters){
		//This array contains all defined fields and it's "white-list-Regex"
		$regexArray = array(
				'MERCHANT_ID' 		=> '[^A-Za-z0-9.]',
				"ACCOUNT" 			=> '[^A-Za-z0-9“”]',
				'ORDER_ID'			=> '[^A-Za-z0-9_-]',
				'AMOUNT'			=> '[^0-9]',
				'CURRENCY'			=> '[^A-Za-z]',
				'TIMESTAMP'			=> '[^0-9]',
				'OFFER_SAVE_CARD'	=> '[^0-9]',
				'PAYER_REF'			=> '[^A-Za-z0-9_""]',
				'PMT_REF'			=> '[^A-Za-z0-9]',
				'PAYER_EXIST'		=> '[^0-9]',
				'RECURRING_TYPE'	=> '[^A-Za-z]',
				'RECURRING_SEQUENCE'=> '[^A-Za-z]',
				"MD5HASH"			=> '[^a-f0-9]',
				"SHA1HASH" 			=> '[^a-f0-9]',
				'AUTO_SETTLE_FLAG'	=> '[^0-1]',
				"COMMENT1" 			=> '[^a-zA-Z0-9\\\\\'@!?%()*:£$&€#[\]|=",+“”._-]',
				"COMMENT2" 			=> '[^a-zA-Z0-9\\\\\'@!?%()*:£$&€#[\]|=",+“”._-]',
				"RETURN_TSS" 		=> '[^0-1]',
				"SHIPPING_CODE" 	=> '[^\/a-zA-Z0-9,|.-\\\\]',
				"SHIPPING_CO" 		=> '[^a-zA-Z0-9,.-]',
				"BILLING_CODE" 		=> '[^\/a-zA-Z0-9,|.-\\\\]',
				"BILLING_CO"		=> '[^a-zA-Z0-9,.-]',
				"CUST_NUM" 			=> '[^a-zA-Z0-9–“”_.,+@]',
				"VAR_REF" 			=> '[^a-zA-Z0-9–“”_.,+@]',
				"PROD_ID" 			=> '[^a-zA-Z0-9–“”_.,+@]'
		);
		
		foreach ($parameters as $field => $value) {
			if(isset($regexArray[$field])){//known parameter (MERCHANT_ID...)
				$parameters[$field] = preg_replace('/' . $regexArray[$field] . '*/', '', $parameters[$field]);
			}else{//unknown parameter (anyting else)
				$parametes[$field] = preg_replace('/[^a-zA-Z0-9\\\\\'@!?%()*:£$&€#[\]|=",+“”._-]*/', '', $parameters[$field]);
			}
		}
	}
}