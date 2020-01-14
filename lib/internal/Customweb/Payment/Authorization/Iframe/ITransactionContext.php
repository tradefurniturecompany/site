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



interface Customweb_Payment_Authorization_Iframe_ITransactionContext extends  
Customweb_Payment_Authorization_ITransactionContext, 
Customweb_Payment_Authorization_IFrontendTransactionContext {
	
	/**
	 * This URL is invoked inside the IFrame when the transaction is completed. 
	 * This is invoked in case it was a success or not. This URL must ensure that the 
	 * user leave the IFrame and get back to the full URL. The next URL where the user has 
	 * to go to must be determine by the script running on this URL.
	 * 
	 * @return string Url
	 */
	public function getIframeBreakOutUrl();
	
	
}