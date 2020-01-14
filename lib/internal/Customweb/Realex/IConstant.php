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
 * This interface provides some constants required for handling the interaction
 * with Realex.
 *        		  	  	 			   
 * @author Mathis Kappeler
 *
 */
interface Customweb_Realex_IConstant {
	
	const STATUS_SUCCESSFUL									= 00;
	const STATUS_TRANSACTION_FAILED							= 1;
	const STATUS_BANK_SYSTEM_ERROR							= 2;
	const STATUS_REALEX_PAYMENT_SYSTEM_ERROR	= 3;
	const STATUS_INCORRECT_XML								= 5;
	const STATUS_CLIENT_DEACTIVATED							= 666;
	
	const BASE_URL = 'https://epage.payandshop.com/';
	const BASE_HPP_URL = 'https://pay.realexpayments.com/';
	const BASE_HPP_TEST_URL = 'https://pay.sandbox.realexpayments.com/';
	
	const HPP_ENDPOINT = 'pay';
	const REMOTE_ENDPOINT = 'epage-remote.cgi';
	const PLUGIN_ENDPOINT = 'epage-remote-plugins.cgi';
	const REDIRECT_ENDPOINT = 'epage.cgi';
	
	const BASE_PAYPAL_URL = 'https://www.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=';
	const BASE_PAYPAL_TEST_URL = 'https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=';
	
	
	//const BASE_PAYPAL_TEST_URL = 'https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_express-checkout&useraction=commit&token=';

	//https://www.paypal.com/cgi-bin/webscr?cmd=_express-checkout&useraction=commit&token=<TOKEN>
}