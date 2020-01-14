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
 * @author Thomas Hunziker
 * @Bean
 *
 */
class Customweb_Realex_Authorization_Iframe_Adapter extends Customweb_Realex_Authorization_AbstractRedirectionAdapter implements Customweb_Payment_Authorization_Iframe_IAdapter {
	
	public function getAuthorizationMethodName() {
		return self::AUTHORIZATION_METHOD_NAME;
	}
	
	public function getAdapterPriority() {
		return 200;
	}
	
	public function createTransaction(Customweb_Payment_Authorization_Iframe_ITransactionContext $transactionContext, $failedTransaction){
		$transaction =  new Customweb_Realex_Authorization_Transaction($transactionContext);
		$transaction->setAuthorizationMethod(self::AUTHORIZATION_METHOD_NAME);
		$transaction->setLiveTransaction(!$this->getConfiguration()->isTestMode());
		return $transaction;
	}
		
	public function getVisibleFormFields(Customweb_Payment_Authorization_IOrderContext $orderContext, $aliasTransaction, $failedTransaction, $customerPaymentContext) {
		return array();
	}
	
	public function getIframeUrl(Customweb_Payment_Authorization_ITransaction $transaction, array $formData) {
		$this->setTransaction($transaction);
		$arguments = $this->getRedirectionArguments($this->getTransaction(), $formData);
		$url = Customweb_Util_Url::appendParameters(
			$arguments['url'],
			$arguments['parameters']
		);
		
		return $url;
	}
	
	public function getIframeHeight(Customweb_Payment_Authorization_ITransaction $transaction, array $formData) {
		return 450;
	}
	
	
	public function finalizeAuthorizationRequest(Customweb_Payment_Authorization_ITransaction $transaction){
		$this->setTransaction($transaction);
		
		$url = Customweb_Util_Url::appendParameters(
			$transaction->getTransactionContext()->getIframeBreakOutUrl(),
			$transaction->getTransactionContext()->getCustomParameters()
		);
		 
	
		return $url;
	}
	

}