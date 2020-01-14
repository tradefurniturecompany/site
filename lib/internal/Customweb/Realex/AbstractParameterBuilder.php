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
 * Basic parameter constructor. It provides basic getter and setter methods.
 *
 * @author Mathis Kappeler
 *
 */
abstract class Customweb_Realex_AbstractParameterBuilder {
	private $configuration;
	private $transaction;
	private $paymentMethod;
	protected $container = null;
	
	public function __construct(Customweb_Realex_Authorization_Transaction $transaction, Customweb_Realex_Configuration $configuration, Customweb_DependencyInjection_IContainer $container) {
		$this->transaction = $transaction;
		$this->configuration = $configuration;
		$this->container = $container;
		$this->paymentMethod = Customweb_Realex_Method_Factory::getMethod($transaction->getPaymentMethod(), $configuration, $this->container);
	}
	
	public function getPaymentMethod() {
		return $this->paymentMethod;
	}
	
	/**
	 * 
	 * @return Customweb_Realex_Configuration
	 */
	protected function getConfiguration(){
		return $this->configuration;
	}
	
	/**
	 *
	 * @return Customweb_Payment_Authorization_PaymentPage_ITransactionContext
	 */
	protected function getTransactionContext(){
		return $this->getTransaction()->getTransactionContext();
	}
	
	/**
	 * @return Customweb_Realex_Authorization_Transaction
	 */
	protected function getTransaction(){
		return $this->transaction;
	}
	
	/**
	 * @return Customweb_Payment_Authorization_IOrderContext
	 */
	public function getOrderContext(){
		return $this->getTransactionContext()->getOrderContext();
	}
}