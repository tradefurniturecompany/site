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
 * This class provides the basic setter and getter method for the adapters.
 * 
 * @author Mathis Kappeler
 *
 */
abstract class Customweb_Realex_AbstractAdapter {
	
	/** 
	 * @var Customweb_Realex_Configuration
	 */
	private $configuration = null;
	
	/** 
	 * @var Customweb_Realex_Authorization_Transaction
	 */
	private $transaction = null;
	
	/**
	 * @var array
	 */
	private $parameters = null;
	private $container = null;
	
	public function __construct(Customweb_Payment_IConfigurationAdapter $configurationAdapter, Customweb_DependencyInjection_IContainer $container) {
		$this->configuration = new Customweb_Realex_Configuration($configurationAdapter);
		$this->container = $container;
	}
	
	/**
	 * @return Customweb_Realex_Configuration
	 */
	public final function getConfiguration(){
		return $this->configuration;
	}
	
	/**
	 * @return Customweb_Realex_Authorization_Transaction
	 */
	public final function getTransaction(){
		return $this->transaction;
	}
	
	/**
	 * @param array $parameters
	 */
	public final function setTransaction($transaction){
		$this->transaction = $transaction;
	}
	/**
	 * @return Customweb_DependencyInjection_IContainer
	 */
	protected function getContainer(){
		return $this->container;
	}
	
}