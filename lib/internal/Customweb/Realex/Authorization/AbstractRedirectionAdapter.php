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
 * Provides common functions for handling the redirection authorization methods.
 * 
 * @author Mathis Kappeler
 *
 */
abstract class Customweb_Realex_Authorization_AbstractRedirectionAdapter extends Customweb_Realex_Authorization_AbstractAdapter{
	
	private $cache = array();
	
	public function getVisibleFormFields(Customweb_Payment_Authorization_IOrderContext $orderContext,
		$aliasTransaction,
		$failedTransaction,
		$paymentCustomerContext){
	
		$elements = array();
		
		if ($this->getConfiguration()->isRealVaultCvcSecurityMeasureActive()) {
			if($aliasTransaction != null && $aliasTransaction != 'new'){
				$elements[] = Customweb_Form_ElementFactory::getCVCElement('CVC');
			}
		}
		return $elements;
	}
	
	public function processAuthorizationCustom(Customweb_Payment_Authorization_ITransaction $transaction, array $parameters){
		$this->setTransaction($transaction);
		$aliasTransaction = $this->getTransaction()->getTransactionContext()->getAlias();
	
		if($this->isRealVaultTransactionPossible($transaction->getTransactionContext()->getOrderContext(), $aliasTransaction)){
			if ($this->getConfiguration()->isRealVaultCvcSecurityMeasureActive()) {
				if ($this->getConfiguration()->isRealVaultCvcSecurityMeasureActive()) {
					if (isset($parameters['cend'])) {
						$cvc = $this->getTransaction()->decode($parameters['cend']);
						$this->getTransaction()->setCvc($cvc);
					}
					$cvc = $this->getTransaction()->getCvc();
					if (!isset($cvc) || strlen($cvc) < 3) {
						$reason = Customweb_I18n_Translation::__('Invalid CVC provided.');
						$this->getTransaction()->setAuthorizationFailed($reason);
						return $this->getTransaction()->getFailedUrl();
					}
				}
			}
			$processor = new Customweb_Realex_Authorization_RealVault_XmlAuthorizationProcessor($this->getConfiguration(), $this->getTransaction(), $this->getContainer());
			$processor->process();
		}
		else {
			$this->setParametersArray($parameters);
			$this->setTransactionParameterFields($parameters);
			$this->getTransaction()->setAuthorizationParameters($parameters);
				
			if(!$this->isResponseHashValid()) {
				$this->getTransaction()->setAuthorizationFailed(new Customweb_Payment_Authorization_ErrorMessage(
					Customweb_I18n_Translation::__('The payment failed. The response data has been tampered. Please contact the merchant.'),
					Customweb_I18n_Translation::__('The response hash seems to be manipulated, hence the transaction was rejected.')
				));
				return $this->getTransaction()->getFailedUrl();
			}
				
			if (!$this->areResponseFieldsValid()) {
				$this->getTransaction()->setAuthorizationFailed(new Customweb_Payment_Authorization_ErrorMessage(
					Customweb_I18n_Translation::__('The payment failed. The response data has been tampered. Please contact the merchant.'),
					Customweb_I18n_Translation::__('The response seems to be manipulated, hence the transaction was rejected.')
				));
				return $this->getTransaction()->getFailedUrl();
			}
				
			if ($parameters['RESULT'] == Customweb_Realex_IConstant::STATUS_SUCCESSFUL) {
				$this->processSuccessfulAuthorization();
			}
			else {
				$this->getTransaction()->setAuthorizationFailed(
						Customweb_Realex_Util::getErrorMessage($parameters['RESULT'], $parameters['MESSAGE'])
				);
			}
		}
		return $this->finalizeAuthorizationRequest($this->getTransaction());
	}
	
	
	
	
	/**
	 * Here we handle the transaction when the RC is 00 for the Paymentpage
	 *
	 * @return void
	 */
	protected function processSuccessfulAuthorization(){
		$parameters = $this->getParametersArray();
		
		if (isset($parameters['PASREF'])) {
			$this->getTransaction()->setPaymentId($parameters['PASREF']);
		}
		else {
			$this->getTransaction()->setPaymentId($this->getTransaction()->getExternalTransactionId());
		}
	
		$this->getTransaction()->authorize();
		$handler = new Customweb_Realex_Authorization_LiabilityHandler($this->getTransaction(), $this->getConfiguration());
		$handler->apply();
	
		//Set RealVault Alias if settings say so
		if(isset($parameters['PMT_SETUP']) && $parameters['PMT_SETUP'] == '00' && $this->getTransaction()->getTransactionContext()->getAlias() == 'new'){
			$this->getTransaction()->setMaskedCardNumber($parameters['SAVED_PMT_DIGITS']);
			$aliasForDisplay = Customweb_Realex_Util::getAliasString(
				$parameters['SAVED_PMT_DIGITS'],
				$parameters['SAVED_PMT_TYPE']
			);
			
			$this->getTransaction()->setAliasForDisplay($aliasForDisplay);
				
			if($this->getConfiguration()->isRecurringSequenceOn()){
				//Set up for following recurring payments
				$parameters['recurring_payment_count'] = 0;
				$this->setParametersArray($parameters);
			}
		}
	
		if (!$this->getTransaction()->isCaptureDeferred()) {
			$this->getTransaction()->capture();
		}
	}
	
	
	private function isResponseHashValid(){
		$parameters = $this->getParametersArray();
		$stringToHash = $this->getResponseStringToHash();
		$hash = new Customweb_Realex_Hash_Hash(
			$stringToHash,
			$this->getConfiguration()->getSignatureKey(),
			$this->getConfiguration()->getEncriptionAlgorithm());
	
		if($hash->isHashValid($parameters[$hash->getHashKeyUppercase()])){
			return true;
		}
		else{
			return false;
		}
	}
	
	private function getResponseStringToHash(){
		$parameters = $this->getParametersArray();
		$hashElements = array($parameters['TIMESTAMP'] ,
			$parameters['MERCHANT_ID'],
			$parameters['ORDER_ID'],
			$parameters['RESULT'],
			$parameters['MESSAGE'],
			$parameters['PASREF'],
			$parameters['AUTHCODE']);
	
		return Customweb_Realex_Util::generateStringToHash($hashElements);
	}
	
	final protected function getRedirectionArguments(Customweb_Realex_Authorization_Transaction $transaction, array $formData) {
		if (!isset($this->cache[$transaction->getExternalTransactionId()])) {
			$this->setTransaction($transaction);
			$parameters = array();
			$url = '';
			try {
				$aliasTransaction = $this->getTransaction()->getTransactionContext()->getAlias();
				if($this->isRealVaultTransactionPossible($transaction->getTransactionContext()->getOrderContext(), $aliasTransaction)){
					if($this->getConfiguration()->isRealVaultCvcSecurityMeasureActive() && (!isset($formData['CVC']) || strlen($formData['CVC']) < 3)){
						$reason = Customweb_I18n_Translation::__('The entered CVC is too short.');
						$this->getTransaction()->setAuthorizationFailed($reason);
						$url = $this->getTransaction()->getFailedUrl();
					}
					else {
						$parameterArray = $this->getParametersArray();
						$parameterArray['cw_transaction_id'] = $this->getTransaction()->getExternalTransactionId();
						$url = $this->getContainer()->getBean('Customweb_Payment_Endpoint_IAdapter')->getUrl("process", "common", $parameterArray);
						if(isset($formaData['CVC'])){
							$parameters['cend'] = $transaction->encrypt($formData['CVC']);
						}
					}
				}
				else {
					if($this->getConfiguration()->isTestEnvironment() || $this->getConfiguration()->isTestMode()){
						$url = Customweb_Realex_IConstant::BASE_HPP_TEST_URL .Customweb_Realex_IConstant::HPP_ENDPOINT; 
					}else{
						$url = Customweb_Realex_IConstant::BASE_HPP_URL .Customweb_Realex_IConstant::HPP_ENDPOINT;
					}
					
					$builder = new Customweb_Realex_Authorization_RedirectionParameterBuilder($this->getTransaction(), $this->getConfiguration(), $this->getContainer());
					$parameters = $builder->buildParameters();
				}
			}
			catch (Exception $e) {
				$url = $this->getTransaction()->getFailedUrl();
				$this->getTransaction()->setAuthorizationFailed($e->getMessage());
			}
	
			$this->cache[$this->getTransaction()->getExternalTransactionId()] = array(
				'url' => $url,
				'parameters' => $parameters,
			);
		}
		return $this->cache[$transaction->getExternalTransactionId()];
	}
	
	private function areResponseFieldsValid(){
		$parameters = $this->getParametersArray();
		if(!(Customweb_Realex_Util::formatAmount(
			$this->getTransaction()->getTransactionContext()->getOrderContext()->getOrderAmountInDecimals(),
			$this->getTransaction()->getTransactionContext()->getOrderContext()->getCurrencyCode()) == $parameters['AMOUNT'])){
			return false;
		}
		if($this->getTransaction()->getTransactionContext()->getOrderContext()->getCurrencyCode() != $parameters['MY_CURRENCY']){
			return false;
		}
		return true;
	}
	
	private function setTransactionParameterFields($parameters){
		if(isset($parameters['CVNRESULT'])){
			$this->getTransaction()->setCVNResult($parameters['CVNRESULT']);
		}
		if(isset($parameters['ECI'])){
			$this->getTransaction()->setECI($parameters['ECI']);
		}
		if(isset($parameters['TSS'])){
			$this->getTransaction()->setTSS($parameters['TSS']);
		}
		if(isset($parameters['AVSADDRESSRESULT'])){
			$this->getTransaction()->setAVSAdressResult($parameters['AVSADDRESSRESULT']);
		}
		if(isset($parameters['AVSPOSTCODERESULT'])){
			$this->getTransaction()->setAVSPostCodeResult($parameters['AVSPOSTCODERESULT']);
		}
	
		if(isset($parameters['PASREF'])){
			$this->getTransaction()->setPasref($parameters['PASREF']);
		}
	
		if(isset($parameters['AUTHCODE'])){
			$this->getTransaction()->setAuthcode($parameters['AUTHCODE']);
		}
	}
	
}