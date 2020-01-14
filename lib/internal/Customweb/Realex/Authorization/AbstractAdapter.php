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
 * This class provides common methods for authorization adapters.
 * 
 * @author Mathis Kappeler
 *
 */
abstract class Customweb_Realex_Authorization_AbstractAdapter extends Customweb_Realex_AbstractAdapter{
	
	private $requestParamters = array();
	
	public function __construct(Customweb_Payment_IConfigurationAdapter $configurationAdapter, Customweb_DependencyInjection_IContainer $container) {
		parent::__construct($configurationAdapter, $container);
	}
	
	
	protected function getEncriptedSensitiveData($cardno, $cvc){
		$md = $cardno . '$' . $cvc;
		return $this->getTransaction()->encrypt($md);
	}

	protected function getEncriptedAndUnsetSensitiveData(){
		$md = $this->getEncriptedSensitiveData($this->getTransaction()->getCardNo(), $this->getTransaction()->getCvc());
		return $md;
	}
	
	
	/**
	 * This function is called fist. 
	 * 	- The sensitive data es encripted
	 *  - The sensitive data together with the other parameters are send to the common endpoint where the regular process starts 
	 *  
	 * @param Customweb_Payment_Authorization_ITransaction $transaction
	 * @param array $parameters
	 */
	public function processAuthorization(Customweb_Payment_Authorization_ITransaction $transaction, array $parameters) {
		$this->setTransaction($transaction);
		
		if(isset($parameters['cardno']) && isset($parameters['CVC'])){
			$parameters['MD'] = $this->getEncriptedSensitiveData($parameters['cardno'], $parameters['CVC']);
			unset($parameters['cardno']);
			unset($parameters['CVC']);
		}
		$usedParameters = array(
			'MD' => $parameters['MD'],
			'CCH' => $parameters['CCH'],
			'CCEM' => $parameters['CCEM'],
			'CCEY' => $parameters['CCEY'],
		
		);
		$this->getTransaction()->setAuthorizationParameters($usedParameters);		
		return $this->getUrlWithParameter('common', $usedParameters);
	}
	

	protected function getUrlWithParameter($endpoint, $parameters = array()){
		if($this->getTransaction()->getCvc() != null && $this->getTransaction()->getCardNo() != null){
			$parameters['MD'] = $this->getEncriptedAndUnsetSensitiveData();
		}
		$parameters['cw_transaction_id'] = $this->getTransaction()->getExternalTransactionId();
		
		$url = $this->getContainer()->getBean('Customweb_Payment_Endpoint_IAdapter')->getUrl("process", $endpoint, $parameters);
		
		$response = new Customweb_Core_Http_Response();
		$response->setLocation($url);
		
		return $response;
	}
	
	public function isAuthorizationMethodSupported(Customweb_Payment_Authorization_IOrderContext $orderContext){
		return true;
	}
	
	public function preValidate(Customweb_Payment_Authorization_IOrderContext $orderContext,
			Customweb_Payment_Authorization_IPaymentCustomerContext $paymentContext){
		
	}

	public function validate(Customweb_Payment_Authorization_IOrderContext $orderContext, Customweb_Payment_Authorization_IPaymentCustomerContext $paymentContext, array $formData) {
		return true;
	}
	
	public function isDeferredCapturingSupported(Customweb_Payment_Authorization_IOrderContext $orderContext, Customweb_Payment_Authorization_IPaymentCustomerContext $paymentContext) {
		return true;
	}
	
	public function getParametersArray() {
		return $this->requestParamters;
	}
	
	public function setParametersArray($parameters) {
		if(!is_null($parameters)){
			$this->requestParamters = $parameters;
			return $this;
		}
		$this->requestParamters = array();
		return $this;
	}
	
	/**
	 * Redirects the customer to the success or failed page as given
	 * by the transaction context.
	 *
	 * @param Customweb_Payment_Authorization_ITransaction $transaction
	 * @return void
	 */
	public function finalizeAuthorizationRequest(Customweb_Payment_Authorization_ITransaction $transaction){
		$this->setTransaction($transaction);
			
		$url = '';
		if ($this->getTransaction()->isAuthorizationFailed()) {
			return $this->getTransaction()->getFailedUrl();
		}
	
		if ($this->getTransaction()->isAuthorized()) {
			return $this->getTransaction()->getSuccessUrl();
		}
		
		return $url;
	}
	
	final protected function isRealVaultTransactionPossible(Customweb_Payment_Authorization_IOrderContext $currentOrderContext, $aliasTransaction) {
		if ($aliasTransaction === null) {
			return false;
		}
		if ($aliasTransaction === 'new') {
			return false;
		}
		if (!($aliasTransaction instanceof Customweb_Realex_Authorization_Transaction)) {
			return false;
		}
		
		if ($this->getConfiguration()->isRealVaultAddressCheckMeasureActive()) {
			if (!Customweb_Util_Address::compareShippingAddresses($currentOrderContext, $aliasTransaction->getTransactionContext()->getOrderContext())) {
				return false;
			}
		}
		
		return true;
	}
	
}

