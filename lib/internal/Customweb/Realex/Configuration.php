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
 * @author Mathis Kappeler
 *
 */
final class Customweb_Realex_Configuration{
	
	/**
	 *       		  	  	 			   
	 * @var Customweb_Payment_IConfigurationAdapter
	 */
	private $configurationAdapter = null;
	
	
	public function __construct(Customweb_Payment_IConfigurationAdapter $configurationAdapter) {
		$this->configurationAdapter = $configurationAdapter;
	}
	
	public function getMerchantId(){
		return $this->getConfigurationValue('merchant_id');
	}
	
	public function getMotoMerchantId(){
		return $this->getConfigurationValue('merchant_id_moto');
	}
	
	public function isDirectCapturingActivated(){
		if($this->getConfigurationValue('capturing_mode') == 'direct'){
			return true;	
		}else{
			return false;
		}
	}
	
	public function isAVSActive(){
		if($this->getConfigurationValue('avs_manager') == 'active'){
			return true;
		}else{
			return false;
		}
	}	
	
	/**
	 * 
	 * @param string $avs Returned AVS
	 * @return boolean True: If the given $avs leads to a certain transaction, according to the shop-settings
	 */
	public function isAVSPostCodeCertain($avs){
		if(in_array($avs, $this->getConfigurationValue('avs_post_code_setting'))){
			return true;
		}
		return false;
	}	
	
	/**
	 * 
	 * @param string $avs Returned AVS
	 * @return boolean True: If the given $avs leads to a certain transaction, according to the shop-settings
	 */
	public function isAVSAddressCertain($avs){
		if(in_array($avs, $this->getConfigurationValue('avs_address_setting'))){
			return true;
		}
		return false;
	}
		
	public function getAutoSettleFlag(){
		return $this->getConfigurationValue('auto_settle_flag');
	}
	
	public function getEncriptionAlgorithm(){
		return $this->getConfigurationValue('encryption_algo');
	}
	
	public function getCVNSetting(){
		return $this->getConfigurationValue('cvn_result');
	}
	
	public function getRefundPassword(){
		return $this->getConfigurationValue('rebate_password');
	}
	
	public function getECICode(){
		return $this->getConfigurationValue('eci_code');
	}
	
	public function get3DSetting(){
		return $this->getConfigurationValue('threed_secure_setting');
	}
	
	public function isRecurringSequenceOn(){
		if($this->getConfigurationValue('recurring_sequence') == 'recurring_sequence_on'){
			return true;
		}else{
			return false;
		}
		return ;
	}
	
	public function getSubaccount(){
		$subaccount = $this->getConfigurationValue('subaccount');
		
		if($this->isTestMode() && !$this->isTestEnvironment()){
			$subaccount = $subaccount . 'test';
		}
		
		return $subaccount;
	}
	
	public function getTSSValue(){
		return $this->getConfigurationValue('tss_value');
	}
	
	public function getSignatureKey(){
		return $this->getConfigurationValue('signature_key');
	}
	
	public function merchantWantAllTransactionToBeCertain(){
		if($this->getConfigurationValue('no_uncertain_transactions') == 'yes'){
			return true;
		}else{
			return false;
		}
	}
	
	public function getTransactionIdSchema() {
		return $this->getConfigurationValue('transaction_id_schema');
	}
	
	public function getShopId(){
		return $this->getConfigurationValue('shop_id');
	}
	
	public function isTestEnvironment(){
		return $this->getConfigurationValue('operation_mode') == 'test_environment';
	}
	
	public function isTestMode(){
		return $this->getConfigurationValue('operation_mode') == 'test';
	}
	
	public function isLiveMode() {
		return $this->getConfigurationValue('operation_mode') == 'live';
	}
	
	public function isAcceptOnly3DSecureTransactionActive() {
		return strolower($this->getConfigurationValue("threed_secure_setting")) == 'force_3d';
	}
	
	public function isAcceptUnenrolledCardsActive() {
		return strolower($this->getConfigurationValue("threed_secure_setting")) == 'accept_unenrolled';
	}
	
	public function is3DSecureActive() {
		return $this->getConfigurationValue("threed_secure_setting") != 'no_3d';
	}
	
	public function isRealVaultCvcSecurityMeasureActive() {
		return $this->getConfigurationValue('real_vault_security_measure') == 'cvc';
	}
	
	public function isRealVaultAddressCheckMeasureActive() {
		return $this->getConfigurationValue('real_vault_security_measure') == 'address_check';
	}
	
	protected function getConfigurationValue($key, $language = null) {
		return $this->configurationAdapter->getConfigurationValue($key, $language);
	}
	
}