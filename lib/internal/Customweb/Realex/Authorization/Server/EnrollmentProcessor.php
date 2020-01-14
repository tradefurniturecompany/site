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
 * This class is responsible for processing the 3D secure enrollment. The 3D secure process
 * affects only Visa or Mastercard. The enrollment check is required to check if a certain
 * card requires the customer to be redirected to the bank for the 3-D secure challenge.
 * 
 * @author Thomas Hunziker
 *
 */
final class Customweb_Realex_Authorization_Server_EnrollmentProcessor extends Customweb_Realex_Xml_AbstractProcessor {
	
	const STATE_ACS_REDIRECTION = 'redirection';
	const STATE_CONTINUE_AUTHORIZATION = 'authorization';
	
	private $acsUrl = null;
	private $acsPareq = null;
	private $state = null;
	
	public function __construct(Customweb_Realex_Configuration $configuration, Customweb_Realex_Authorization_Transaction $transaction, Customweb_DependencyInjection_IContainer $container) {
		$builder = new Customweb_Realex_Authorization_Server_EnrollmentBuilder($transaction, $configuration, $container);
		parent::__construct($configuration, $builder, $transaction, $container);
	}
	
	protected function getEndpoint() {
		return Customweb_Realex_IConstant::REMOTE_ENDPOINT;
	}
	
	public function getAcsUrl() {
		return $this->acsUrl;
	}
	
	public function getAcPareq() {
		return $this->acsPareq;
	}
	
	public function getState() {
		return $this->state;
	}
	
	public function process(){
		$xml = $this->processWithStatusCheck();
		if ($xml->result == Customweb_Realex_IConstant::STATUS_SUCCESSFUL) {
			$this->acsUrl = (string) $xml->url;
			$this->acsPareq = (string) $xml->pareq;
			if (empty($this->acsUrl) || empty($this->acsPareq)) {
				throw new Exception(Customweb_I18n_Translation::__('The 3-D secure enrollment does not return the ASC URL or the ASC pareq parameter.'));
			}
			$this->state = self::STATE_ACS_REDIRECTION;
		}
		else if ($xml->result == '110' && $xml->enrolled == 'N') {
			if (!$this->getConfiguration()->isAcceptOnly3DSecureTransactionActive()) {
				$brand = strtolower($this->getTransaction()->getCardBrand());
				if ($paymentMethod == 'visa') {
					$this->getTransaction()->setECI(6);
				}
				else {
					$this->getTransaction()->setECI(1);
				}
				$this->state = self::STATE_CONTINUE_AUTHORIZATION;
			}
			else {
				throw new Exception(Customweb_I18n_Translation::__('Your card is not enrolled for 3-D secure. The merchant accepts only cards which are enrolled.'));
			}
		}
		else {
			throw new Exception(Customweb_I18n_Translation::__('The 3-D secure enrollement status of your card could not be checked.'));
		}
	}
}

