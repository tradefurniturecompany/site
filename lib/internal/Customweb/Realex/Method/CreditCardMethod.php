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
final class Customweb_Realex_Method_CreditCardMethod extends Customweb_Realex_Method_DefaultMethod {
	
	private $transaction;

	public function getFormFields(Customweb_Payment_Authorization_IOrderContext $orderContext, $aliasTransaction, $failedTransaction, $authorizationMethod, $isMoto, $customerPaymentContext) {
		
		$elements = array();
	
		/* @var $aliasTransaction Customweb_Realex_Authorization_Transaction */
		/* @var $failedTransaction Customweb_Realex_Authorization_Transaction */
		
		$formBuilder = new Customweb_Payment_Authorization_Method_CreditCard_ElementBuilder();
			
		// Set field names
		$formBuilder
		->setCardHolderFieldName('CCH')
		->setCardNumberFieldName('cardno')
		->setExpiryMonthFieldName('CCEM')
		->setExpiryYearFieldName('CCEY')
		->setExpiryYearNumberOfDigits(2);
		
		if ($aliasTransaction === null || $aliasTransaction === 'new' && !$this->getGlobalConfiguration()->isRealVaultCvcSecurityMeasureActive()) {
			$formBuilder->setCvcFieldName('CVC');
		}
		
		$formBuilder->setCardHandler($this->getCardHandler());
		
		if($isMoto){
			$formBuilder->setForceCvcOptional();
		}
		
		// Handle brand selection
		if (strtolower($this->getPaymentMethodName()) == 'creditcard') {
			$formBuilder->setAutoBrandSelectionActive(true);
		}
		else {
			$formBuilder
			->setFixedBrand(true)
			->setSelectedBrand($this->getPaymentMethodName())
			->setCardHandlerByBrandInformationMap($this->getPaymentInformationMap(), $this->getPaymentMethodName(), 'brand')
			;
		}
		
		if ($failedTransaction !== null) {
			$cardHolderName = $failedTransaction->getCardHolderName();
			if (isset($cardHolderName)) {
				$formBuilder->setCardHolderName($cardHolderName);
			}
			
			$cardExpiryMonth = $failedTransaction->getCardExpiryMonth();
			if (isset($cardExpiryMonth)) {
				$formBuilder->setSelectedExpiryMonth($cardExpiryMonth);
			}
			
			$cardExpiryYear = $failedTransaction->getCardExpiryYear();
			if (isset($cardExpiryYear)) {
				$formBuilder->setSelectedExpiryYear($cardExpiryYear);
			}
		}
			
		// Set context values
		$formBuilder->setCardHolderName($orderContext->getBillingFirstName() . ' ' . $orderContext->getBillingLastName());
		
		if($aliasTransaction !== null && $aliasTransaction !== 'new'){
			$formBuilder->setFixedCardExpiryActive();
			$formBuilder->setFixedCardExpiryActive();
			$formBuilder->setSelectedExpiryMonth($aliasTransaction->getCardExpiryMonth());
			$formBuilder->setSelectedExpiryYear($aliasTransaction->getCardExpiryYear());
			$formBuilder->setMaskedCreditCardNumber($aliasTransaction->getMaskedCardNumber());
		}
		return array_merge($elements, $formBuilder->build());
	}
	
	public function getCardHandler() {
		if ($this->getPaymentMethodName() == 'creditcard') {
			$cardInformations = Customweb_Payment_Authorization_Method_CreditCard_CardInformation::getCardInformationObjects(
					$this->getPaymentInformationMap(),
					$this->getPaymentMethodConfigurationValue('credit_card_brands'),
					'PaymentMethod'
			);
				
		}
		else {
			$cardInformations = Customweb_Payment_Authorization_Method_CreditCard_CardInformation::getCardInformationObjects(
					$this->getPaymentInformationMap(),
					$this->getPaymentMethodName(),
					'PaymentMethod'
			);
		}
		
		if ($this->getGlobalConfiguration()->isTestMode() || $this->getGlobalConfiguration()->isTestEnvironment()) {
			foreach($cardInformations as $information) {
				/* @var $information Customweb_Payment_Authorization_Method_CreditCard_CardInformation */
				if ($information->getBrandKey() == 'visa') {
					$information->appendCardNumberLength(17);
					$information->appendCardNumberLength(19);
				}
			}
		}
		
		return new Customweb_Payment_Authorization_Method_CreditCard_CardHandler($cardInformations);
	}
	
	public function getPaymentMethodHashParam($transaction){
		return $transaction->getCardNo();
	}
	
	/**
	 * Returns the card month expiry date (two digits).
	 *
	 * @return string
	 */
	protected function getCardMonthExpiryDate($transaction) {
		/* @var $transaction Customweb_Realex_Authorization_Transaction */
		return $transaction->getCardExpiryMonth();
	}
	
	/**
	 * Returns the card year expiry date (two digits).
	 *
	 * @return string
	 */
	protected function getCardYearExpiryDate($transaction) {
		/* @var $transaction Customweb_Realex_Authorization_Transaction */
		return $transaction->getCardExpiryYear();
	}
	
	public function getPaymentMethodDetailsElement($transaction, $payerref = ""){
		/* @var $transaction Customweb_Realex_Authorization_Transaction */
		
		if ($transaction->getCardNo() === null) {
			throw new Exception("No card number provided. To use the getCardDataElement() a card number must be provided.");
		}
	
		return
		"<card>" .
			$payerref .	
			"<number>" . $transaction->getCardNo() . "</number>
			<expdate>" . $this->getCardMonthExpiryDate($transaction) . $this->getCardYearExpiryDate($transaction) . "</expdate>
			<type>" . $transaction->getCardBrandName() . "</type>
			<chname>" . $transaction->getCardHolderName() . "</chname>" .
		"</card>";
	}
	
	public function is3dProcessSupported(){
		return true;
	}

	public function setTransactionDataOnUserInput($parameters, $transaction) {
		$handler = $this->getCardHandler();
		$this->transaction = $transaction;
		
		$cardno = $this->transaction->getCardNo();
		$cvc = $this->transaction->getCvc();
		
		// In case the cardno is set, then all other parameters must be also set.
		if (!isset($cardno)) {
			return;
		}
	
		if (!isset($cvc)) {
			throw new Exception(Customweb_I18n_Translation::__("No CVC code set."));
		}
	
		if (!isset($parameters['CCH'])) {
			throw new Exception(Customweb_I18n_Translation::__("No card holder set."));
		}
	
		if (!isset($parameters['CCEM'])) {
			throw new Exception(Customweb_I18n_Translation::__("No card expiry month set."));
		}
	
		if (!isset($parameters['CCEY'])) {
			throw new Exception(Customweb_I18n_Translation::__("No card expiry year set."));
		}
	
		// Handle cardno
		if (isset($cardno)) {
			// Set Brand
			$brandKey = $handler->getBrandKeyByCardNumber($cardno);
			$brandName = $handler->mapBrandNameToExternalName($brandKey);
			$transaction->setCardBrandName($brandName);
			$transaction->setCardBrandKey($brandKey);
			$transaction->setMaskedCardNumber(Customweb_Realex_Util::maskCardNumber($cardno));
				
			//!!!Important this field has to be unset before the shop/API serializes this sensitive field!!!
			$transaction->setCardNo($cardno);
			unset($cardno);
		}
		
		if(strlen($transaction->getCvc()) > 2){
			$handler->validateCardNumberAndCvc($transaction->getCardNo(), $transaction->getCvc());
		}
		
		
	
		// Expiry Date
		if (isset($parameters['CCH']) && $transaction->getCardHolderName() === null) {
			$transaction->setCardHolderName(str_replace(array('<', '>'), array('', ''), $parameters['CCH']));
		}
	
		$month = preg_replace('/[^0-9]+/', '', $parameters['CCEM']);
		$year = preg_replace('/[^0-9]+/', '', $parameters['CCEY']);
	
		if (strlen($month) != 2) {
			throw new Exception(Customweb_I18n_Translation::__("No card expiry month is invalid."));
		}
	
		if (strlen($year) != 2) {
			throw new Exception(Customweb_I18n_Translation::__("No card expiry year is invalid."));
		}
		$transaction->setCardExpiryDate($month, $year);
		
		unset($parameters['MD']);
		
		if(is_null($parameters)){
			return array();
		}
		
		return $parameters;
	}
}