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
 * This class provides the connection handling with the remote server over 
 * the XML protocol. 
 * 
 * It is responsible to create the XML, the sending of the XML document and
 * the evaluation of the XML.
 * 
 * @author Thomas Hunziker
 *
 */
abstract class Customweb_Realex_Xml_AbstractProcessor {
	
	/**
	 * @var Customweb_Realex_Xml_IBuilder
	 */
	private $builder = null;
	
	/**
	 * @var SimpleXMLElement
	 */
	private $xmlResponse = null;
	
	/**
	 * @var Customweb_Realex_Configuration
	 */
	private $configuration = null;
	
	private $transaction = null;
	
	protected $container = null;
	
	
	public function __construct(Customweb_Realex_Configuration $configuration, Customweb_Realex_Xml_IBuilder $builder, Customweb_Realex_Authorization_Transaction $transaction, Customweb_DependencyInjection_IContainer $container) {
		$this->builder = $builder;
		$this->configuration = $configuration;
		$this->transaction = $transaction;
		$this->container = $container;
	}
	
	/**
	 * @return Customweb_Realex_Authorization_Transaction
	 */
	protected function getTransaction() {
		return $this->transaction;
	}
	
	/**
	 * Returns the endpoint to which the XML should be sent to.
	 * 
	 * @return string
	 */
	abstract protected function getEndpoint();
	
	/**
	 * This method process the given request and reply with the 
	 * given action. It may throw exceptions.
	 * 
	 * @throws Exception
	 * @return void
	 */
	abstract public function process();
	
	/**
	 * @return Customweb_Realex_Configuration
	 */
	public final function getConfiguration(){
		return $this->configuration;
	}
	
	
	/**
	 * @return Customweb_Realex_Xml_IBuilder
	 */
	protected function getBuilder() {
		return $this->builder;
	}
	
	/**
	 * Returns the xml response of the request. This may be null in
	 * case the process fails or it was no executed.
	 * 
	 * @return SimpleXMLElement
	 */
	protected function getXmlResponse() {
		return $this->xmlResponse;
	}
	
	
	/**
	 * This method sends the given XML to the remote service. This
	 * method checks the signature (Hash) of the response.
	 *
	 * @throws Exception
	 * @throws Customweb_Realex_Exception_PaymentErrorException
	 * @return SimpleXMLElement
	 */
	protected  final function processWithoutStatusCheck() {
		$handler = Customweb_Realex_Util::sendXML($this->getBuilder()->buildXml(), $this->getEndpoint());
		$this->xmlResponse = new SimpleXMLElement($handler->getBody());
		$this->checkHash();
		return $this->xmlResponse;
	}
	
	/**
	 * This method process the XML with hash check and response code. In case the response code
	 * is not successful an exception with the precise error message is thrown.
	 * 
	 * @throws Customweb_Realex_Exception_PaymentErrorException
	 * @return SimpleXMLElement
	 */
	protected final function processWithStatusCheck() {
		$handler = Customweb_Realex_Util::sendXML($this->getBuilder()->buildXml(), $this->getEndpoint());
		$this->xmlResponse = new SimpleXMLElement($handler->getBody());
		
		if($this->xmlResponse->result == Customweb_Realex_IConstant::STATUS_SUCCESSFUL){
			$this->checkHash();
			
			return $this->xmlResponse;
		}
		else {			
			throw new Customweb_Realex_Exception_PaymentErrorException(
				Customweb_Realex_Util::getErrorMessage($this->xmlResponse->result, $this->xmlResponse->message)
			);
		}
	}
	
	/**
	 * This method checks if the given XML does contain a correct hash and
	 * hence we can thrust the response.
	 *
	 * @throws Exception In case the hash is invalid.
	 */
	private function checkHash(){
		$orderContext = $this->transaction->getTransactionContext()->getOrderContext();
		$paymentMethod = Customweb_Realex_Method_Factory::getMethod($orderContext->getPaymentMethod(), $this->getConfiguration(), $this->container);
		
		
		$stringToHash = Customweb_Realex_Util::getResponseXMLStringToHash($this->xmlResponse, $paymentMethod);
		$xmlHashkey = Customweb_Realex_Util::getLowerCaseEncriptionXMLKey($this->getConfiguration());
		$hash = new Customweb_Realex_Hash_Hash(
			$stringToHash,
			$this->getConfiguration()->getSignatureKey(),
			$this->getConfiguration()->getEncriptionAlgorithm());
		$returnHash = $this->xmlResponse->{$xmlHashkey};
	
		if(!$hash->isHashValid($returnHash)){
			throw new Exception(
				Customweb_I18n_Translation::__('The calculated and returned hash do not match.')
			);
		}
	}
	
}