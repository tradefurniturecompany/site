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
 * This class represents a payer. A payer has a unique identification key and 
 * a status (active / inactive).
 * 
 * The payer can only be created over the static factory method.
 * 
 * @author Thomas Hunziker
 *
 */
final class Customweb_Realex_Authorization_RealVault_Payer {
	private $payerReference = null;
	private $active = false;
	private function __construct(Customweb_Realex_Authorization_Transaction $transaction) {
		$this->payerReference = $transaction->getExternalTransactionId() . Customweb_Util_Rand::getRandomString(30 - strlen($transaction->getExternalTransactionId()));
	}
	
	public function setActive() {
		$this->active = true;
		return $this;
	}
	
	public function isActive() {
		return $this->active;
	}
	
	public function getPayerReference() {
		return $this->payerReference;
	}

	/**
	 * @param Customweb_Realex_Authorization_Transaction $transaction
	 * @return Customweb_Realex_Authorization_RealVault_Payer
	 */
	public static function getPayerByTransaction(Customweb_Realex_Authorization_Transaction $transaction) {
		$customerContext = $transaction->getTransactionContext()->getPaymentCustomerContext();
		$customerMap = $customerContext->getMap();
		if(isset($customerMap['realvault_payer'])){
			$payer = $customerMap['realvault_payer'];
		}else{
			$payer = new Customweb_Realex_Authorization_RealVault_Payer($transaction);
			$customerContext->updateMap(array('realvault_payer' => $payer));
		}
		
		return $payer;
	}
	
}