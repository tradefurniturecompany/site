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
 * This class is the entry point for the Realex payment page 
 * authorization integration. We use the redirect method to implement the
 * payment page.
 * 
 * The class is responsible for the redirection and the processing of the notifciation.
 *
 * @author Mathis Kappeler
 * @Bean
 *
 */
final class Customweb_Realex_Authorization_PaymentPage_Adapter extends Customweb_Realex_Authorization_AbstractRedirectionAdapter implements Customweb_Payment_Authorization_PaymentPage_IAdapter {
	
	public function getAdapterPriority() {
		return 100;
	}
	
	public function getAuthorizationMethodName() {
		return self::AUTHORIZATION_METHOD_NAME;
	}
	
	public function createTransaction(Customweb_Payment_Authorization_PaymentPage_ITransactionContext $transactionContext, $failedTransaction){
		$transaction =  new Customweb_Realex_Authorization_Transaction($transactionContext);
		$transaction->setAuthorizationMethod(self::AUTHORIZATION_METHOD_NAME);
		$transaction->setLiveTransaction(!$this->getConfiguration()->isTestMode());
		return $transaction;
	}
	
	public function getRedirectionUrl(Customweb_Payment_Authorization_ITransaction $transaction, array $formData){
		$this->setTransaction($transaction);
	
		$arguments = $this->getRedirectionArguments($this->getTransaction(), $formData);
		$url =  Customweb_Util_Url::appendParameters(
			$arguments['url'],
			$arguments['parameters']
		);
		return $url; 
	}
	
	public function isHeaderRedirectionSupported(Customweb_Payment_Authorization_ITransaction $transaction, array $formData){
		$this->setTransaction($transaction);
		if (strlen($this->getRedirectionUrl($this->getTransaction(), $formData)) < 2000) {
			return true;
		}
		else {
			return false;
		}
	}
	
	public function getParameters(Customweb_Payment_Authorization_ITransaction $transaction, array $formData){
		$this->setTransaction($transaction);
	
		$arguments = $this->getRedirectionArguments($this->getTransaction(), $formData);
	
		return $arguments['parameters'];
	}
	
	public function getFormActionUrl(Customweb_Payment_Authorization_ITransaction $transaction, array $formData){
		$this->setTransaction($transaction);
		$arguments = $this->getRedirectionArguments($this->getTransaction(), $formData);
		return $arguments['url'];
	}
	
	
}
