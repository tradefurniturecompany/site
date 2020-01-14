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
 * This class provides common methods for authorizations over the remote interface.
 * 
 * @author Mathis Kappeler
 *
 */
abstract class Customweb_Realex_Authorization_AbstractRemoteAdapter extends Customweb_Realex_Authorization_AbstractAdapter {
	
	/**
	 * This method is called when the transaction is processed and non alias transaction 
	 * was processed.
	 * 
	 * @return void
	 */
	abstract protected function processNonAliasAuthorization();
	
	protected function extractSensitiveData($parameters){
		if(isset($parameters['MD'])){
			$cardno_cvc = $this->getTransaction()->decode($parameters['MD']);
			$cardno_cvc = explode('$', $cardno_cvc);
			if(sizeof($cardno_cvc) > 1){
				$this->getTransaction()->setCardNo($cardno_cvc[0]);
				$this->getTransaction()->setCvc($cardno_cvc[1]);
			}
		}
	}
	
	public function processAuthorizationCustom(Customweb_Payment_Authorization_ITransaction $transaction, array $parameters) {		
		$this->setTransaction($transaction);
		$this->setParametersArray($parameters);
		
		$this->extractSensitiveData($parameters);
	
		try {
			$this->setTransactionDataOnUserInput($parameters);
		} catch(Exception $e) {
			$this->getTransaction()->setAuthorizationFailed($e->getMessage());
			return $this->getTransaction()->getFailedUrl();
		}
	
		$parameters = $this->getParametersArray();
		
		$aliasTransaction = $this->getTransaction()->getTransactionContext()->getAlias();
		if($aliasTransaction !== null && $aliasTransaction !== 'new'){
			$processor = new Customweb_Realex_Authorization_RealVault_XmlAuthorizationProcessor($this->getConfiguration(), $this->getTransaction(), $this->getContainer());
			$processor->process();
		}
		else {
			$response = $this->processNonAliasAuthorization();
			if(isset($response->paymentmethoddetails->SetExpressCheckoutResponse->Token)){
				$token = $response->paymentmethoddetails->SetExpressCheckoutResponse->Token;
				if($this->getConfiguration()->isTestEnvironment()){
					return Customweb_Realex_IConstant::BASE_PAYPAL_TEST_URL . $token;
				}else{
					return Customweb_Realex_IConstant::BASE_PAYPAL_URL . $token;
				}
			}
		}
		return $this->finalizeAuthorizationRequest($this->getTransaction());
	}
	
	public function paypal(Customweb_Payment_Authorization_ITransaction $transaction, array $parameters) {
		$this->setTransaction($transaction);
		/* @var $transaction Customweb_Realex_Authorization_Transaction */
		if(isset($parameters['PayerID'])){//Success
			$this->getTransaction()->setPayerId($parameters['PayerID']);
			$this->getTransaction()->setToken($parameters['token']);
			$this->getTransaction()->setPaypalPasRef($parameters['pasref']);
			
			$processor = new Customweb_Realex_Authorization_Server_ApmAuthorizationProcessor($this->getConfiguration(), $this->getTransaction(), $this->getContainer());
			$response = $processor->process();
			
			if($this->getTransaction()->isAuthorized()){
				return $this->getTransaction()->getSuccessUrl();
			}else{
				return $this->getTransaction()->getSuccessUrl();
			}
			
		}else{//Failed
			$reason = Customweb_I18n_Translation::__("The redirect response from PayPal was unsuccessful.");
			$transaction->setAuthorizationFailed($reason);
			return $transaction->getFailedUrl();
		}
	}
	
	protected function unsetSensitiveData(){
		//!!!Very important unset cardno and cvc on transaction!!!
		$this->getTransaction()->setCardNo(null);
		$this->getTransaction()->setCvc(null);
	}
	
	public function finalizeAuthorizationRequest(Customweb_Payment_Authorization_ITransaction $transaction){
		$this->setTransaction($transaction);
		
		$this->unsetSensitiveData();
	
		if($this->getTransaction()->isAuthorizationFailed() || $this->getTransaction()->isAuthorized()){
			return parent::finalizeAuthorizationRequest($this->getTransaction());
		}else{
			$parameterArray = $this->getParametersArray();
			$parameterArray['cw_transaction_id'] = $this->getTransaction()->getExternalTransactionId();
			return $this->getContainer()->getBean('Customweb_Payment_Endpoint_IAdapter')->getUrl("process", "common", $parameterArray);
		}
	}
	
	
	/**
	 * This method handles the user input and may throw an exception on invalid user input.
	 * 
	 * @throws Exception
	 */
	protected function setTransactionDataOnUserInput($parameters) {
		if(!is_null($parameters)){
			$parameters = Customweb_Realex_Method_Factory::getMethod($this->getTransaction()->getPaymentMethod(), $this->getConfiguration(), $this->getContainer())->setTransactionDataOnUserInput($parameters, $this->getTransaction());
			
			$this->setParametersArray($parameters);
		}
	}
}

