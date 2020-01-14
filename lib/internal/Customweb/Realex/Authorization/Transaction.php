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
 * Overrides the default transaction object and adds additional flags and variables
 * required.
 * 
 * @author Mathis Kappeler
 *
 */
final class Customweb_Realex_Authorization_Transaction extends Customweb_Payment_Authorization_DefaultTransaction{

	private $eciCode = null;
	
	//used for payment method-ref in case of a real vault initialisation
	private $pmRef = null;
	
	private $avsPostCodeResult = null;
	private $avsAddressResult = null;
	private $cvnResult = null;
	private $tss = null;
	private $pasref = null;
	private $authcode = null;
	private $cardHolderName = null;
	private $cardExpiryYear = null;
	private $cardExpiryMonth = null;
	private $cardBrandName = null;
	private $cardBrandKey = null;
	private $threeDSecureCAVV = null;
	private $threeDSecureXid = null;
	private $maskedCardNumber = null;
	private $formattedTransactionId = null;
	
	private $accountHolder = null;
	private $iban = null;
	private $bic = null;
	private $giroBankNumber = null;
	private $idealBankName = null;
	
	private $acsUrl = null;
	private $acPareq = null;
	private $cardno = null;
	private $cvc = null;
	
	private $token = null;
	private $payerId = null;
	private $payPalPasRef = null;
	
	public function __construct(Customweb_Payment_Authorization_ITransactionContext $transactionContext) {
		parent::__construct($transactionContext);
		$this->pmRef = $this->getExternalTransactionId() . Customweb_Util_Rand::getRandomString(30 - strlen($this->getExternalTransactionId()));
	}
	
	public function getFormattedTransactionId(Customweb_Realex_Configuration $configuration) {
		if ($this->formattedTransactionId === null) {
			$this->formattedTransactionId = Customweb_Payment_Util::applyOrderSchema(
				$configuration->getTransactionIdSchema(),
				$this->getExternalTransactionId(),
				64
			);
		}
		
		return $this->formattedTransactionId;
	}
	
	/**
	 * This method returns true, when the transaction should not be captured / settled 
	 * directly after the authorization.
	 * 
	 * @return boolean True, when the the capturing is done later.
	 */
	public function isCaptureDeferred() {
		if(!$this->getPaymentMethod()->existsPaymentMethodConfigurationValue('capturing')){
			return false;
		}
		if ($this->getTransactionContext()->getCapturingMode() === null) {
			$capturing = $this->getPaymentMethod()->getPaymentMethodConfigurationValue('capturing');
			if (strtolower($capturing) == 'direct') {
				return false;
			}
			else {
				return true;
			}
		}
		else {
			if ($this->getTransactionContext()->getCapturingMode() == Customweb_Payment_Authorization_ITransactionContext::CAPTURING_MODE_DEFERRED) {
				return true;
			}
			else {
				return false;
			}
		}
	}
	
	public function getFormattedAuthorizationAmount() {
		return Customweb_Realex_Util::formatAmount(
			$this->getTransactionContext()->getOrderContext()->getOrderAmountInDecimals(),
			$this->getTransactionContext()->getOrderContext()->getCurrencyCode()
		);
	}

	public function isCaptureClosable() {
		// We support only one partial capture.
		return false;
	}
	
	public function getECI(){
		return $this->eciCode;
	}
	
	public function setECI($eciCode){
		$this->eciCode = (string)$eciCode;
	}
	
	public function getAcsUrl(){
		return $this->acsUrl;
	}
	public function setAcsUrl($acsUrl){
		$this->acsUrl = (string)$acsUrl;
	}
	
	public function getAcPareq(){
		return $this->acPareq;
	}
	public function setAcPareq($acPareq){
		$this->acPareq = (string)$acPareq;
	}
	
	
	public function getCardNo(){
		return $this->cardno;
	}
	public function setCardNo($cardno){
		if (isset($cardno) && $cardno !== null) {
			$this->cardno = (string) preg_replace('/[^0-9]+/', '', $cardno);
		}
	}
	
	public function getCvc(){
		return $this->cvc;
	}
	public function setCvc($cvc){
		if (isset($cvc) && $cvc !== null) {
			$this->cvc = (string) preg_replace('/[^0-9]+/', '', $cvc);
		}
	}
	
	public function getPayerId(){
		return $this->payerId;
	}
	public function setPayerId($payerId){
		$this->payerId = (string) $payerId;
	}
	
	public function getToken(){
		return $this->token;
	}
	public function setToken($token){
		$this->token = (string)$token;
	}
	
	public function getPaypalPasRef(){
		return $this->payPalPasRef;
	}
	
	public function setPaypalPasRef($payPalPasRef){
		$this->payPalPasRef = (string)$payPalPasRef;
	}
	
	public function getAVSPostCodeResult(){
		return $this->avsPostCodeResult;
	}
	
	public function setAVSPostCodeResult($avsPostCodeResult){
		$this->avsPostCodeResult = (string) $avsPostCodeResult;
	}
	
	public function getAVSAddressResult(){
		return $this->avsPostCodeResult;
	}
	
	public function setAVSAdressResult($avsAddressResult){
		$this->avsAddressResult = (string) $avsAddressResult;
	}

	public function setCVNResult($cvnResult){
		$this->cvnResult = (string) $cvnResult;
	}
	
	public function getCVNResult(){
		return $this->cvnResult;
	}
	
	public function setTSS($tss){
		$this->tss = (string) $tss;
	}
	
	public function getTSS(){
		return $this->tss;
	}
	
	public function setMaskedCardNumber($number) {
		$this->maskedCardNumber = (string) $number;
		return $this;
	}
	
	public function getMaskedCardNumber() {
		return $this->maskedCardNumber;
	}
	
	public function setCardHolderName($name) {
		$this->cardHolderName = (string) $name;
		return $this;
	}
	
	public function getCardHolderName() {
		return $this->cardHolderName;
	}
	
	public function setCardExpiryDate($month, $year) {
		$this->cardExpiryMonth = (string) $month;
		$this->cardExpiryYear = (string) $year;
		return $this;
	}
	
	public function getCardExpiryYear() {
		return $this->cardExpiryYear;
	}
	
	public function getCardExpiryMonth() {
		return $this->cardExpiryMonth;
	}
	
	public function setPasref($pasref){
		$this->pasref = (string) $pasref;
	}

	public function getPasref(){
		return $this->pasref;
	}
	
	public function getAuthcode(){
		return $this->authcode;
	}
	
	public function setAuthcode($authcode){
		$this->authcode = (string) $authcode;
	}

	public function getFailedUrl() {
		return Customweb_Util_Url::appendParameters(
				$this->getTransactionContext()->getFailedUrl(),
				$this->getTransactionContext()->getCustomParameters()
		);
	}
	
	public function getSuccessUrl() {
		return Customweb_Util_Url::appendParameters(
				$this->getTransactionContext()->getSuccessUrl(),
				$this->getTransactionContext()->getCustomParameters()
		);
	}
	
	public function getNotificationUrl() {
		return Customweb_Util_Url::appendParameters(
				$this->getTransactionContext()->getNotificationUrl(),
				$this->getTransactionContext()->getCustomParameters()
		);
	}
	
	public function encrypt($string) {
		return base64_encode($this->getCipher()->encrypt($string));
	}
	
	public function decode($string) {
		return $this->getCipher()->decrypt(base64_decode($string));
	}
	
	public function getCardBrandName() {
		return $this->cardBrandName;
	}
	
	public function setCardBrandName($name) {
		$this->cardBrandName = (string) $name;
		return $this;
	}
	
	public function getCardBrandKey() {
		return $this->cardBrand;
	}
	
	public function setCardBrandKey($key) {
		$this->cardBrand = (string) $key;
		return $this;
	}
	
	public function setPMRef($paymentMethodReference){
		$this->pmRef = (string) $paymentMethodReference;
	}
	
	public function getPMRef(){
		return $this->pmRef;
	}
	
	public function set3DSecureCAVV($cavv) {
		$this->threeDSecureCAVV = (string) $cavv;
		return $this;
	}
	
	public function get3DSecureCAVV() {
		return $this->threeDSecureCAVV;
	}
	
	public function set3DSecureXid($xid) {
		$this->threeDSecureXid = (string) $xid;
		return $this;
	}
	
	public function get3DSecureXid() {
		return $this->threeDSecureXid;
	}
	
	public function setAccountHolder($accountHolder){
		$this->accountHolder = (string) $accountHolder;
	}
	
	public function getAccountHolder(){
		return $this->accountHolder;
	}
	
	public function setIban($iban){
		$this->iban = (string) $iban;
	}
	
	public function getIban(){
		return $this->iban;
	}
	
	public function setBic($bic){
		$this->bic = (string)$bic;
	}
	
	public function getBic(){
		return $this->bic;
	}
	
	public function setGiroBankNumber($giroBankNumber){
		$this->giroBankNumber = (string) $giroBankNumber;
	}
	
	public function getGiroBankNumber(){
		return $this->giroBankNumber; 
	}
	
	public function setIdealBankName($idealBankName){
		$this->idealBankName = (string) $idealBankName;
	}
	
	public function getIdealBankName(){
		return $this->idealBankName;
	}
	
	
	public function getTransactionSpecificLabels() {
		$labels = array();
		
		$params = $this->getAuthorizationParameters();
		
		if(isset($params['PAYMENTMETHOD'])){
			$labels['payment_method'] = array(
					'description' => Customweb_I18n_Translation::__('The Paymentmethod the customer choose on the paymentpage.'),
					'label' => Customweb_I18n_Translation::__('Payment Method'),
					'value' => $params['PAYMENTMETHOD']
			);
		}
		
		$accountHolder = $this->getAccountHolder();
		if (isset($accountHolder)) {
			$labels['account_holder'] = array(
					'description' => Customweb_I18n_Translation::__('This is the name of the account holder).'),
					'label' => Customweb_I18n_Translation::__('Account Holder'),
					'value' => $this->getAccountHolder()
			);
		}

		$alias = $this->getAlias();
		if (isset($alias)) {
			$labels['alias'] = array(
					'description' => Customweb_I18n_Translation::__('This is the technical alias token (Alias Manger/Realvault).'),
					'label' => Customweb_I18n_Translation::__('Alias Token'),
					'value' => $this->getAlias()
			);
		}
		
		$avsPostCodeResult = $this->getAVSPostCodeResult();
		if (isset($avsPostCodeResult)) {
			$labels['avs_post_code'] = array(
					'description' => Customweb_I18n_Translation::__('Address Verification Result. <br>(M (Matched) <br>N (Not Matched) <br>I (Problem with check) <br>U (Unable to check (not certified etc)) <br>P (Partial Match))'),
					'label' => Customweb_I18n_Translation::__('AVS Post Code Check'),
					'value' => $this->getAVSPostCodeResult()
			);
		}
		
		$avsAddressResult = $this->getAVSAddressResult();
		if (isset($avsAddressResult)) {
			$labels['avs_address'] = array(
					'description' => Customweb_I18n_Translation::__('Address Verification Result. <br>(M (Matched) <br>N (Not Matched) <br>I (Problem with check) <br>U (Unable to check (not certified etc)) <br>P (Partial Match))'),
					'label' => Customweb_I18n_Translation::__('AVS Address Check'),
					'value' => $this->getAVSAddressResult()
			);
		}
		
		$cvnResult = $this->getCVNResult();
		if (isset($cvnResult)) {
			$labels['cvn_result'] = array(
					'description' => Customweb_I18n_Translation::__('CVN Check Result. <br>(M: CVV Matched), <br>(N: CVV Not Matched), <br>(I: CVV Not checked due to circumstances), <br>(U: CVV Not checked – issuer not certified), <br>(P: CVV Not Processed)'),
					'label' => Customweb_I18n_Translation::__('CVN Check Result'),
					'value' => $this->getCVNResult()
			);
		}
		
		$eci = $this->getECI();
		if (isset($eci)) {
			$labels['eci'] = array(
					'description' => Customweb_I18n_Translation::__('ECI (Ecommerce Indicator)'),
					'label' => Customweb_I18n_Translation::__('ECI Value'),
					'value' => $this->getECI()
			);
		}
		
		$tss = $this->getTSS();
		if (isset($tss)) {
			$labels['tss'] = array(
					'description' => Customweb_I18n_Translation::__('TSS (Transaction Suitability Scoring) system'),
					'label' => Customweb_I18n_Translation::__('TSS Value'),
					'value' => $this->getTSS()
			);
		}
		return $labels;
	}
	
	/**
	 * @param void
	 * @return string encoded-string 
	 */
	private function getCipher() {
		$cipher = new Crypt_AES(CRYPT_AES_MODE_CBC);
		$cipher->setKey($this->getPMRef());
		return $cipher;
	}
}