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
 * This class handles transactions after they are authorized. The class sets
 * the liability depending on the configuration done by the merchant and
 * the flags sets during the authorization.
 * 
 * The class is responsible to sets the 'uncertain' flag on the transaction
 * depending on other flags.
 * 
 * @author Mathis Kappeler
 *
 */
final class Customweb_Realex_Authorization_LiabilityHandler {
	
	private $configuration = null;
	private $transaction = null;
	
	public function __construct(Customweb_Realex_Authorization_Transaction $transaction, Customweb_Realex_Configuration $configuration) {
		$this->configuration = $configuration;
		$this->transaction = $transaction;
	}
	
	/**
	 * @return Customweb_Realex_Authorization_Transaction
	 */
	public function getTransaction() {
		return $this->transaction;
	}
	
	/**
	 * @return Customweb_Realex_Configuration
	 */
	public function getConfiguration() {
		return $this->configuration;
	}
	
	/**
	 * This method applies liability shift on the given transaction according to the 
	 * given configuration.
	 * 
	 * @return void
	 */
	public function apply() {
		if(!$this->getConfiguration()->merchantWantAllTransactionToBeCertain()){
			$this->checkCVNResult();
			$this->checkTSS();
			$this->checkAVS();
			$this->checkECICode();
		}
	}
	
	/**
	 *
	 * Set transaction to uncertain according to the ECI-shop-settings
	 *
	 * @param void
	 * @return void
	 *
	 */
	private function checkECICode(){
		$eci_codes_chain = '_' . implode('_', $this->getConfiguration()->getECICode()) . '_' ;
	
		if ($this->getTransaction()->isAuthorizationUncertain()){
			// nothing to do if transaction is already uncertain
			return;
		}
	
		$eci = $this->getTransaction()->getECI();
		if(!isset($eci)){
			return;
		}
	
		if(strpos($eci_codes_chain, '_' . $this->getTransaction()->getECI() . '_') === false){
			//Markes the transaction as uncertain, according to the Shop-settings
			$this->getTransaction()->setAuthorizationUncertain();
		}
	}
	
	/**
	 * Set transaction to uncertain based on the CVN-result according to the shop-settings
	 *
	 * @return void
	 */
	private function checkCVNResult(){
		if ($this->getTransaction()->isAuthorizationUncertain()){
			// nothing to do if transaction is already uncertain
			return;
		}
	
		$cvnResult = $this->getTransaction()->getCVNResult();
		if(!isset($cvnResult)){
			return;
		}
	
		if(in_array($this->getTransaction()->getCVNResult(), $this->getConfiguration()->getCVNSetting())){
			//Markes the transaction as uncertain, according to the Shop-settings
			$this->getTransaction()->setAuthorizationUncertain();
		}
	}
	
	/**
	 * Set transaction to uncertain based on the TSS-result according to the shop-settings
	 *
	 * @return void
	 */
	private function checkTSS(){
		if ($this->getTransaction()->isAuthorizationUncertain()) {
			// nothing to do if transaction is already uncertain
			//OR TSS is not set
			return;
		}
		$tss = $this->getTransaction()->getTSS();
		if(!isset($tss)){
			return;
		}
	
		if($this->getTransaction()->getTSS() < $this->getConfiguration()->getTSSValue()){
			//Markes the transaction as uncertain, according to the Shop-settings;
			$this->getTransaction()->setAuthorizationUncertain();
		}
	}
	
	private function checkAVS(){
		if(!$this->getConfiguration()->isAVSActive()){
			return;
		}
		if ($this->getTransaction()->isAuthorizationUncertain()) {
			// nothing to do if transaction is already uncertain
			return;
		}
	
		$avsPostCodeResult = $this->getTransaction()->getAVSPostCodeResult();
		$avsAddressResult = $this->getTransaction()->getAVSAddressResult();
	
		if(!isset($avsPostCodeResult) && !isset($avsAddressResult)){
			return;
		}
	
		//Set Transaction if eigther AVSPostCode or AVSAddress does not meet the demand (Shop settings)
		if(!$this->getConfiguration()->isAVSPostCodeCertain($this->getTransaction()->getAVSPostCodeResult()) || !$this->getConfiguration()->isAVSAddressCertain($this->getTransaction()->getAVSAddressResult())){
			$this->getTransaction()->setAuthorizationUncertain();
		}
	}
}