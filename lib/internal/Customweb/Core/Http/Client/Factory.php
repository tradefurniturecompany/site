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
 * This factory allows to create a HTTP client. The implementation of 
 * the client may selected depending on the environment.
 * 
 * @author Thomas Hunziker
 *
 */
final class Customweb_Core_Http_Client_Factory {

	private function __construct() {
		
	}
	
	/**
	 * Returns a HTTP client.
	 * 
	 * @return Customweb_Core_Http_IClient
	 */
	public static function createClient() {
		
		// Currently we support only a socket implementation. Later we 
		// may add other implementations (cURL etc.).
		return new Customweb_Core_Http_Client_Socket();
	}
	
	
}