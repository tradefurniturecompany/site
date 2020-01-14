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
 * The operation describes the operation.
 * 
 * @author Thomas Hunziker
 *
 */
interface Customweb_Payment_BackendOperation_IOperation {
	
	/**
	 * This operation indicates the moment, when the merchant sends the
	 * products to the customer.
	 *
	 * @var string
	 */
	const SHIPPING_OPERATION_ID = 'shipping';
	
	/**
	 * @var string
	 */
	const CAPTURE_OPERATION_ID = 'capture';
	
	/**
	 * @var string
	 */
	const CANCELLATION_OPERATION_ID = 'cancellation';
	
	/**
	 * @var string
	 */
	const REFUND_OPERATION_ID = 'refund';
	
	
	/**
	 * This method returns the human readable and translated name of the operation.
	 * 
	 * @return string Operation Name
	 */
	public function getName();
	
	/**
	 * This method returns a translated description of the operation.
	 * 
	 * @return string Operation Description
	 */
	public function getDescription();
	
	/**
	 * This method returns an identifier for the operation. This must not be translated and
	 * it must not be human readable. It should only contains ASCII chars.
	 * 
	 * @return string Operation Identifier
	 */
	public function getIdentifier();
	
	/**
	 * This method indicats whether this method can process order modifications. In case
	 * the operation accepts order modifications, the shop may provide a new order context 
	 * which differs from the original order context.
	 * 
	 * @return boolean 
	 */
	public function canAcceptOrderModifications();
	
}