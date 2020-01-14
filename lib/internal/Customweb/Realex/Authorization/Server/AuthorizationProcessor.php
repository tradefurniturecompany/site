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
 * This class is responsible to process a remote transaction.
 * 
 * @author Mathis Kappeler
 *
 */
final class Customweb_Realex_Authorization_Server_AuthorizationProcessor extends Customweb_Realex_Authorization_AbstractXmlProcessor {
	
	public function __construct(Customweb_Realex_Configuration $configuration, Customweb_Realex_Authorization_Transaction $transaction, Customweb_DependencyInjection_IContainer $container) {
		parent::__construct($configuration, new Customweb_Realex_Authorization_Server_AuthorizationBuilder($transaction, $configuration, $container), $transaction, $container);
		
	}
	
	public function process() {
		parent::process();
	
		if (!$this->getTransaction()->isAuthorizationFailed()) {
			//Set up RealVault (Alias manager), if this is an initial Transaction (Pure RealVault or Initial Recurrring payment)
			if($this->isRealVaultAliasCreationRequired()){
				$this->setUpRealVault();
			}
		}
	}
	
	/**
	 * RealVault is the name Realex Payments have given to the Alias-manager-system
	 *
	 * @return void
	 */
	private function setUpRealVault(){
		// Setup Real Vault Payer
		try {
			$processor = new Customweb_Realex_Authorization_Server_RealVault_SetupPayerProcessor($this->getConfiguration(), $this->getTransaction(), $this->container);
			$processor->process();
		}
		catch(Exception $e) {
			$this->getTransaction()->addErrorMessage($e->getMessage());
		}
		
	
		// Setup Real Vault Payment method
		try {
			$processor = new Customweb_Realex_Authorization_Server_RealVault_SetupPaymentMethodProcessor($this->getConfiguration(), $this->getTransaction(), $this->container);
			$processor->process();
		}
		catch(Customweb_Realex_Exception_PaymentErrorException $e) {
			$this->getTransaction()->addErrorMessage($e->getErrorMessage());
		}
		catch(Exception $e) {
			$this->getTransaction()->addErrorMessage($e->getMessage());
		}
	}
	
	private function isRealVaultAliasCreationRequired(){
		// In case we are processing a recurring transaction, we want always an alias.
		if ($this->getTransaction()->getTransactionContext()->createRecurringAlias()) {
			return true;
		}
		if($this->getTransaction()->getTransactionContext()->getAlias() == 'new'){		
			return true;
		}
		return false;
	}
}

