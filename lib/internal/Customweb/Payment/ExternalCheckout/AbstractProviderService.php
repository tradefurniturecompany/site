<?php

/**
 *  * You are allowed to use this API in your web application.
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
 * Abstract implementation of the provider service.
 * 
 * @author Thomas Hunziker
 *
 */
abstract class Customweb_Payment_ExternalCheckout_AbstractProviderService implements Customweb_Payment_ExternalCheckout_IProviderService {

	public function getCheckouts(Customweb_Payment_ExternalCheckout_IContext $context) {
		$checkouts = $this->getCheckoutsUnfiltered();
		$result = array();
		foreach ($checkouts as $checkout) {
			if ($checkout->isActive() && $checkout->checkMinimalOrderTotal($context->getOrderAmountInDecimals()) && $checkout->checkMaximalOrderTotal($context->getOrderAmountInDecimals())) {
				$result[] = $checkout;
			}
		}
		
		return $result;
	}
	
	/**
	 * Returns a list of checkouts unfiltered. 
	 * 
	 * @return Customweb_Payment_ExternalCheckout_AbstractCheckout
	 */
	abstract public function getCheckoutsUnfiltered();
	
}