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
 * @Controller("process")
 *
 */
class Customweb_Realex_Endpoint_Process extends Customweb_Payment_Endpoint_Controller_Abstract {
	
	/**
	 * @var Customweb_Core_ILogger
	 */
	private $logger;
	
	/**
	 * @param Customweb_DependencyInjection_IContainer $container
	 */
	public function __construct(Customweb_DependencyInjection_IContainer $container) {
		parent::__construct($container);
		$this->logger = Customweb_Core_Logger_Factory::getLogger(get_class($this));
	}
	
	
	/**
	 *
	 * @Action("index")
	 */
	public function process(Customweb_Payment_Authorization_ITransaction $transaction, Customweb_Core_Http_IRequest $request) {
		$this->logger->logInfo("The notification process has been started for the transaction " . $transaction->getTransactionId() . ".");
		$adapter = $this->getAdapterFactory()->getAuthorizationAdapterByName($transaction->getAuthorizationMethod());
	
		$isAuthorized = $transaction->isAuthorized();
		$parameters = $request->getParameters();
		$url = $adapter->processAuthorization($transaction, $parameters);

		$this->logger->logInfo("The notification process has been finished for the transaction " . $transaction->getTransactionId() . ".");
		return $this->getHttpResponse($url);
	}	

	
	/**
	 *
	 * @Action("common")
	 */
	public function common(Customweb_Payment_Authorization_ITransaction $transaction, Customweb_Core_Http_IRequest $request) {
		$this->logger->logInfo("The notification process has been started for the transaction " . $transaction->getTransactionId() . ".");
		$adapter = $this->getAdapterFactory()->getAuthorizationAdapterByName($transaction->getAuthorizationMethod());
	
		$isAuthorized = $transaction->isAuthorized();
		$parameters = $request->getParameters();
	
		$url = $adapter->processAuthorizationCustom($transaction, $parameters);
		$this->logger->logInfo("The notification process has been finished for the transaction " . $transaction->getTransactionId() . ".");
		return $this->getHttpResponse($url);
	}
	
	/**
	 *
	 * @Action("aclreturn")
	 */
	public function aclreturn(Customweb_Payment_Authorization_ITransaction $transaction, Customweb_Core_Http_IRequest $request) {
		$this->logger->logInfo("The aclreturn process has been started for the transaction " . $transaction->getTransactionId() . ".");
		$adapter = $this->getAdapterFactory()->getAuthorizationAdapterByName($transaction->getAuthorizationMethod());
	
		$isAuthorized = $transaction->isAuthorized();
		$parameters = $request->getParameters();
	
		$url = $adapter->aclReturn($transaction, $parameters);
		$this->logger->logInfo("The aclreturn process has been finished for the transaction " . $transaction->getTransactionId() . ".");
		return $this->getHttpResponse($url);
	}
	
	/**
	 *
	 * @Action("paypal")
	 */
	public function paypal(Customweb_Payment_Authorization_ITransaction $transaction, Customweb_Core_Http_IRequest $request){
		$this->logger->logInfo("The paypal return process has been started for the transaction " . $transaction->getTransactionId() . ".");
		$adapter = $this->getAdapterFactory()->getAuthorizationAdapterByName($transaction->getAuthorizationMethod());
		
		$isAuthorized = $transaction->isAuthorized();
		$parameters = $request->getParameters();
		
		$url = $adapter->paypal($transaction, $parameters);
		$this->logger->logInfo("The paypal return process has been finished for the transaction " . $transaction->getTransactionId() . ".");
		return $this->getHttpResponse($url);
	}

	private function getHttpResponse($url){
		if($url instanceof Customweb_Core_Http_Response){
			return $url;
		}
		
		$response = new Customweb_Core_Http_Response();
		
		$body = "<html>
				<body>
					<title>
					Redirect
					</title>	
				</body>
				<script type='text/javascript'>
				<!--
				window.location = '" . $url . "'
				//-->
				</script>
				</html>";
			
		$response->setBody($body);
		return $response;
	}
	

}