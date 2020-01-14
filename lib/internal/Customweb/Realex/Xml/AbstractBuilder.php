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
 * This class provides common methods to build a XML to interacting
 * with Realex.
 * 
 * The class is responsible to provide methods to handle the header,
 * the footer and the hash element of the XML.
 * 
 * @author Thomas Hunziker
 *
 */
abstract class Customweb_Realex_Xml_AbstractBuilder extends Customweb_Realex_AbstractParameterBuilder implements Customweb_Realex_Xml_IBuilder {

	private $timestamp = null;

	public function __construct(Customweb_Realex_Authorization_Transaction $transaction, Customweb_Realex_Configuration $configuration, Customweb_DependencyInjection_IContainer $container) {
		parent::__construct($transaction, $configuration, $container);
		$this->timestamp = strftime("%Y%m%d%H%M%S");
	}
	
	/**
	 * Returns a list of Strings which are part of the hash. 
	 *
	 * @return array
	 */
	abstract protected function getParametersToHash();

	/**
	 * The timestamp on which the hash calculation is based on.
	 *
	 * @return string
	 */
	protected final function getTimestamp() {
		return $this->timestamp;
	}

	protected final function getXmlHeader($type){
		return "<?xml version='1.0' encoding='UTF-8'?><request type='$type' timestamp='{$this->getTimestamp()}'>";
	}

	protected function getBasicElements($addAccount = true){
		$xml = "<merchantid>" . $this->getMerchantid() . "</merchantid>";
		if($addAccount){
			$subaccount = $this->getConfiguration()->getSubaccount();
			$xml = $xml . "<account>" . $subaccount . "</account>";
		}
		return $xml;
	}
	
	protected function getOrderIdElement(){
		$xml = "<orderid>" . $this->getTransaction()->getFormattedTransactionId($this->getConfiguration()) . "</orderid>";
		return $xml;
	}
	
	protected function getMerchantid(){
		return $this->getConfiguration()->getMerchantId();
	}

	protected function getAmountElement($amount) {
		$currency = $this->getOrderContext()->getCurrencyCode();
		$amount = Customweb_Realex_Util::formatAmount(
			$amount,
			$currency
		);
		return "<amount currency='" . $currency . "'>" . $amount . "</amount>";
	}

	/**
	 * This method returns the hash element for this XML.
	 *
	 * @return string
	 */
	protected function getHashElement(){
		$hash = $this->createHash();
		return "<" . $hash->getHashKeyLowercase() . ">" . $hash->getHash() . "</" . $hash->getHashKeyLowercase() . ">";
	}

	/**
	 * The name of the hash element.
	 *
	 * @param Customweb_Realex_Hash_Hash $hash
	 * @return string
	 */
	protected function getHashElementName(Customweb_Realex_Hash_Hash $hash) {
		return $hash->getHashKeyLowercase();
	}

	/**
	 * This method calculates the hash value for this XML.
	 *
	 * @return Customweb_Realex_Hash_Hash
	 */
	protected function createHash() {
		$params = $this->getParametersToHash();
		$stringToHash = Customweb_Realex_Util::generateStringToHash($params);
		$hash = new Customweb_Realex_Hash_Hash(
			$stringToHash,
			$this->getConfiguration()->getSignatureKey(),
			$this->getConfiguration()->getEncriptionAlgorithm()
		);

		return $hash;
	}

	protected function getXMLFooter(){
		return "</request>";
	}

}