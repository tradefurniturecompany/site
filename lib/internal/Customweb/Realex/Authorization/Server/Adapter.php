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
 * This Class is the entry point for the Realex Payments SERVER payments and is 
 * called by the payment_api.
 * 
 * The general process is as follow:
 * 1. Check if the card is a 3 D secure card and if it is enrolled for 3-D secure.
 * 2. In case it is enrolled and a redirection to the bank of the card holder is possible, do the redirection.
 * 3. In case it is enrolled and the customer returns to the store, the signature is verified.
 * 4. The authorization is send to Realex.
 * 
 * @author Mathis Kappeler
 * @Bean
 *
 */
final class Customweb_Realex_Authorization_Server_Adapter extends Customweb_Realex_Authorization_AbstractRemoteAdapter implements Customweb_Payment_Authorization_Server_IAdapter {
	/**
	 * @return Customweb_Payment_Authorization_ITransaction
	 */
	public function createTransaction(Customweb_Payment_Authorization_Server_ITransactionContext $transactionContext, $failedTransaction){
		$transaction =  new Customweb_Realex_Authorization_Transaction($transactionContext);
		$transaction->setAuthorizationMethod($this->getAuthorizationMethodName());
		$transaction->setLiveTransaction(!$this->getConfiguration()->isTestMode());
		return $transaction;
	}
	
	public function getAdapterPriority() {
		return 400;
	}
	
	public function getAuthorizationMethodName() {
		return self::AUTHORIZATION_METHOD_NAME;
	}
	
	public function getVisibleFormFields(Customweb_Payment_Authorization_IOrderContext $orderContext, $aliasTransaction, $failedTransaction, $customerPaymentContext) {
		return Customweb_Realex_Method_Factory::getMethod($orderContext->getPaymentMethod(), $this->getConfiguration(), $this->getContainer())
		->getFormFields($orderContext, $aliasTransaction, $failedTransaction, self::AUTHORIZATION_METHOD_NAME, false, $customerPaymentContext);
	}
	
	protected function processNonAliasAuthorization(){
		$paymentMethodName = strtolower($this->getTransaction()->getPaymentMethod()->getPaymentMethodName());
			
		switch($paymentMethodName){
			case "paypal":
				$processor = new Customweb_Realex_Authorization_Server_ApmAuthorizationProcessor($this->getConfiguration(), $this->getTransaction(), $this->getContainer());
				$response = $processor->process();
				if(isset($response->paymentmethoddetails->SetExpressCheckoutResponse->Token)){
					return $response;
				}else{
					$message = Customweb_I18n_Translation::__("There was no PayPal token returned. Please contact your merchant.");
					throw new Exception($message);
				}
				
				break;
			case "directdebits":
			case "giropay":
			
				$processor = new Customweb_Realex_Authorization_Server_ApmAuthorizationProcessor($this->getConfiguration(), $this->getTransaction(), $this->getContainer());
				$response = $processor->process();
				break;
			default:
				$this->processCreditCard();
				break;
		}
	}
	
	private function processCreditCard(){
		$parameters = $this->getParametersArray();
		
		//If 3DSecure is not wished. We skip 3D and move forward to the plain realAuthorization
		if(!$this->getConfiguration()->is3DSecureActive()) {
			$this->realAuthorization();
		}
		else {
			$this->process3DSecureEnrollment();
		}
	}
	
	
	/**
	 * @var object Customweb_Realex_Authorization_Transaction
	 */
	public function finalizeAuthorizationRequest(Customweb_Payment_Authorization_ITransaction $transaction){
		$this->setTransaction($transaction);
		
		if($this->getTransaction()->getAcPareq() != null && $this->getTransaction()->getAcsUrl() != null){
			$md = $this->getEncriptedAndUnsetSensitiveData();

			
			$parameterArray = $this->getParametersArray();
			$parameterArray['cw_transaction_id'] = $this->getTransaction()->getExternalTransactionId();
			$responseUrl =  $this->getContainer()->getBean('Customweb_Payment_Endpoint_IAdapter')->getUrl("process", "aclreturn", $parameterArray);
			$response = new Customweb_Core_Http_Response();
			
			$body = "<HTML>
			<HEAD>
			<TITLE>" . Customweb_I18n_Translation::__("Redirect") . "</TITLE>
			<SCRIPT LANGUAGE='Javascript' >
			<!--
			function OnLoadEvent() {
				document.form.submit();
			}
			//-->
			</SCRIPT>
			</HEAD>
			<BODY onLoad='OnLoadEvent()'>
			<p>" . Customweb_I18n_Translation::__("You will be redirect to your bank to confirm the payment.") . "</p>
			<FORM NAME='form' ACTION='" . $this->getTransaction()->getAcsUrl() . "' METHOD='POST'>
				<INPUT TYPE='hidden' NAME='PaReq' VALUE='" . $this->getTransaction()->getAcPareq(). "'>
				<INPUT TYPE='hidden' NAME='TermUrl'	VALUE='" . $responseUrl . "'>
				<INPUT TYPE='hidden' NAME='MD' VALUE='". $md . "'>
				<NOSCRIPT><INPUT TYPE='submit'></NOSCRIPT>
			</FORM>
			</BODY>
			</HTML>";
			
			$response->setBody($body);
			
			return $response;
		}else{
			return parent::finalizeAuthorizationRequest($this->getTransaction());
		}
	}
	
	/**
	 * This method may not throw an exception.
	 * 
	 * @return void
	 */
	private function process3DSecureEnrollment() {
		$processor = new Customweb_Realex_Authorization_Server_EnrollmentProcessor($this->getConfiguration(), $this->getTransaction(), $this->getContainer());
		try {
			$processor->process();
			if ($processor->getState() == Customweb_Realex_Authorization_Server_EnrollmentProcessor::STATE_ACS_REDIRECTION) {

				$this->getTransaction()->setAcPareq($processor->getAcPareq());
				$this->getTransaction()->setAcsUrl($processor->getAcsUrl());
			}
		}
		catch(Exception $e) {
			$this->getTransaction()->setAuthorizationFailed($e->getMessage());
		}
	}
	
	public function aclReturn(Customweb_Payment_Authorization_ITransaction $transaction, array $parameters) {
		$this->setTransaction($transaction);
		
		$this->setParametersArray($parameters);
		
		if(!isset($parameters['PaRes'])){
			$msg = Customweb_I18n_Translation::__("At this point the PaRes-URL parameter must be given.");
			throw new Exception($msg);
		}
		
		$this->processACSRespond();
		
		return parent::finalizeAuthorizationRequest($this->getTransaction());
	}
	
	/**
	 * This method may not throw an exception.
	 *
	 * @return void
	 */
	private function processACSRespond(){
		$parameters = $this->getParametersArray();
		
		if (isset($parameters['MD'])) {
			$this->extractSensitiveData($parameters);
		}
		else {
			$this->getTransaction()->setAuthorizationFailed( Customweb_I18n_Translation::__("We can not process the 3-D secure response, because we do not get the additional data transmitted with the redirection to the ACS service."));
		}
	
		try {
			$processor = new Customweb_Realex_Authorization_Server_SignatureProcessor($this->getConfiguration(), $this->getTransaction(), $parameters['PaRes'], $this->getContainer());
			$processor->process();
		}
		catch (Exception $e) {
			$this->getTransaction()->setAuthorizationFailed($e->getMessage());
		}
		
		$this->realAuthorization();
	}
	
	/**
	 * Here the non-3D-authorization is processed
	 *
	 * @param void
	 * @return void
	 */
	protected function realAuthorization(){
		$processor = new Customweb_Realex_Authorization_Server_AuthorizationProcessor($this->getConfiguration(), $this->getTransaction(), $this->getContainer());
		$processor->process();
	}
}