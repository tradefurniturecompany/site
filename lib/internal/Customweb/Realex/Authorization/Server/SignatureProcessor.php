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
 * This class checks whether a given PaRes is valid or not. The PaRes
 * is send back by the customer's browser after the 3-D secure challenge.
 * 
 * @author Mathis Kappeler
 *
 */
final class Customweb_Realex_Authorization_Server_SignatureProcessor extends Customweb_Realex_Xml_AbstractProcessor{
	
	private $pares = null;
	
	public function __construct(Customweb_Realex_Configuration $configuration, Customweb_Realex_Authorization_Transaction $transaction, $pares, Customweb_DependencyInjection_IContainer $container) {
		$builder = new Customweb_Realex_Authorization_Server_SignatureBuilder($transaction, $configuration, $pares, $container);
		parent::__construct($configuration, $builder, $transaction, $container);
	}
	
	protected function getEndpoint() {
		return Customweb_Realex_IConstant::REMOTE_ENDPOINT;
	}
	
	public function process() {
		$xml = $this->processWithoutStatusCheck();		
		if ((string)$xml->result == Customweb_Realex_IConstant::STATUS_SUCCESSFUL) {
			$status = strtoupper((string)$xml->threedsecure->status);
			if ($status == 'Y' || $status == 'A') {
				$this->getTransaction()->setECI((string) $xml->threedsecure->eci);
				$this->getTransaction()->set3DSecureCAVV((string) $xml->threedsecure->cavv);
				$this->getTransaction()->set3DSecureXid((string) $xml->threedsecure->xid);
			}
			else {
				throw new Exception(Customweb_I18n_Translation::__("The 3-D secure authorization failed because of temporary issue with the authentication system."));
			}
		}
		else {
			throw new Exception(Customweb_I18n_Translation::__("The 3-D secure authorization failed."));
		}
	}
}

