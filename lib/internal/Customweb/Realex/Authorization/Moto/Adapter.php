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
 * Generally its required to use a different merchant ID or 
 * a different channel for MoTo. However to authenticate the 
 * remote interface must be used.
 * 
 * @author Thomas Hunziker
 * @Bean
 *
 */
final class Customweb_Realex_Authorization_Moto_Adapter extends Customweb_Realex_Authorization_AbstractRemoteAdapter implements Customweb_Payment_Authorization_Moto_IAdapter{

	/**
	 * @return Customweb_Payment_Authorization_ITransaction
	 */
	public function createTransaction(Customweb_Payment_Authorization_Moto_ITransactionContext $transactionContext, $failedTransaction) {
		$transaction =  new Customweb_Realex_Authorization_Transaction($transactionContext);
		$transaction->setAuthorizationMethod($this->getAuthorizationMethodName());
		$transaction->setLiveTransaction(!$this->getConfiguration()->isTestMode());
		return $transaction;
	}
	
	public function getAdapterPriority() {
		return 1000;
	}
	
	public function getAuthorizationMethodName() {
		return self::AUTHORIZATION_METHOD_NAME;
	}
	
	
	public function getVisibleFormFields(Customweb_Payment_Authorization_IOrderContext $orderContext, $aliasTransaction, $failedTransaction, $paymentCustomerContext) {
		return Customweb_Realex_Method_Factory::getMethod($orderContext->getPaymentMethod(), $this->getConfiguration(), $this->getContainer())
		->getFormFields($orderContext, $aliasTransaction, $failedTransaction, self::AUTHORIZATION_METHOD_NAME, true, $paymentCustomerContext);
	}
	
	
	/**
	 * Here the non-3D-authorization is processed
	 *
	 * @param void
	 * @return void
	 */
	protected function realAuthorization(){
		$processor = new Customweb_Realex_Authorization_Moto_AuthorizationProcessor($this->getConfiguration(), $this->getTransaction(), $this->getContainer());
		$processor->process();
	}
	
	protected function processNonAliasAuthorization(){
		$this->realAuthorization();
	}
	
	public function getFormActionUrl(Customweb_Payment_Authorization_ITransaction $transaction) {
		if (!($transaction instanceof Customweb_Realex_Authorization_Transaction)) {
			throw new Exception("The given transaction is not of type Customweb_Realex_Authorization_Transaction.");
		}
		$parameterArray = array();
		$parameterArray['cw_transaction_id'] = $transaction->getExternalTransactionId();
		return $this->getContainer()->getBean('Customweb_Payment_Endpoint_IAdapter')->getUrl("process", "index", $parameterArray);
	}
	
	public function getParameters(Customweb_Payment_Authorization_ITransaction $transaction) {
		return array();
	}
	
	
}