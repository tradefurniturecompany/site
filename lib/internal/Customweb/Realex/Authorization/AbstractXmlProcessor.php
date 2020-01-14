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
 * This class provides common methods to handle the authorization process. It builds
 * the main interaction with the remote interface.
 * 
 * @author Thomas Hunziker
 *
 */
abstract class Customweb_Realex_Authorization_AbstractXmlProcessor extends Customweb_Realex_Xml_AbstractProcessor {
	
	protected function getEndpoint() {
		return Customweb_Realex_IConstant::REMOTE_ENDPOINT;
	}
	
	public function process() {
		try {
			$result = $this->processWithStatusCheck();
			if(isset($result->paymentmethoddetails->SetExpressCheckoutResponse->Token)){
				//This token is need for redirection to Paypal
				return $result;
			}
			
			$this->setTransactionState();
			
			$handler = new Customweb_Realex_Authorization_LiabilityHandler($this->getTransaction(), $this->getConfiguration());
			$handler->apply();
						
			$this->getTransaction()->authorize();
			if (!$this->getTransaction()->isCaptureDeferred()) {
				$this->getTransaction()->capture();
			}
		}
		catch(Customweb_Realex_Exception_PaymentErrorException $e) {
			$this->getTransaction()->setAuthorizationFailed($e->getErrorMessage());
		}
		catch(Exception $e) {
			$this->getTransaction()->setAuthorizationFailed($e->getMessage());
		}
	}

	final protected function setTransactionState(){
		$xml = $this->getXmlResponse();
		
		if ($xml === null) {
			throw new Exception("This method can be only called, when the process was successful.");
		}
		
		if (isset($xml->pasref)) {
			$this->getTransaction()->setPaymentId((String) $xml->pasref);
		}
		else {
			$this->getTransaction()->setPaymentId($this->getTransaction()->getExternalTransactionId());
		}
		
		if(isset($xml->cvnresult)){
			$this->getTransaction()->setCVNResult((string) $xml->cvnresult);
		}
	
		if(isset($xml->tss) && isset($xml->tss->result)){
			$this->getTransaction()->setTSS((string)$xml->tss->result);
		}
	
		if(isset($xml->avspostcoderesponse)){
			$this->getTransaction()->setAVSPostCodeResult(((string)$xml->avspostcoderesponse));
		}
	
		if(isset($xml->avsaddressresponse)){
			$this->getTransaction()->setAVSAdressResult((string)$xml->avsaddressresponse);
		}
	
		if(isset($xml->pasref)){
			$this->getTransaction()->setPasref((String) $xml->pasref);
		}
	
		if(isset($xml->authcode)){
			$this->getTransaction()->setAuthcode((String) $xml->authcode);
		}
	}
	
}