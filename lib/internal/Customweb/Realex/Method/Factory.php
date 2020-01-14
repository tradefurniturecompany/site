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
 */
final class Customweb_Realex_Method_Factory {
	
	private function __construct() {}
	
	/**
	 * @return Customweb_Realex_Method_DefaultMethod
	 */
	public static function getMethod(Customweb_Payment_Authorization_IPaymentMethod $method, Customweb_Realex_Configuration $config, Customweb_DependencyInjection_IContainer $container) {
		$paymentMethodName = strtolower($method->getPaymentMethodName());
		
		switch($paymentMethodName){	
			case "directdebits" :
				return new Customweb_Realex_Method_DirectDebitsMethod($method, $config, $container);
			case "giropay" :
				return new Customweb_Realex_Method_GiroPayMethod($method, $config, $container);
			case "paypal" :
				return new Customweb_Realex_Method_PaypalMethod($method, $config, $container);
// 			case "sofortueberweisung" :
// 				return new Customweb_Realex_Method_SofortMethod($method, $config);
// 			case "ideal" :
// 				return new Customweb_Realex_Method_IdealMethod($method, $config);
			default:
				return new Customweb_Realex_Method_CreditCardMethod($method, $config);
		}
	}
}