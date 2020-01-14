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
 *
 * @category	Customweb
 * @package		Customweb_RealexCw
 * 
 */

namespace Customweb\RealexCw\Model\Authorization\Method\Context;

interface IContext
{
	/**
	 * @return \Customweb\RealexCw\Model\Payment\Method\AbstractMethod
	 */
	public function getPaymentMethod();

	/**
	 * @return \Customweb\RealexCw\Model\Authorization\OrderContext
	 */
	public function getOrderContext();

	/**
	 * @return \Customweb\RealexCw\Model\Authorization\CustomerContext
	 */
	public function getCustomerContext();

	/**
	 * @return null|string|int
	 */
	public function getAliasTransaction();

	/**
	 * @return \Customweb\RealexCw\Model\Authorization\Transaction
	 */
	public function getTransaction();

	/**
	 * @return \Magento\Sales\Model\Order
	 */
	public function getOrder();

	/**
	 * @return \Magento\Quote\Model\Quote
	 */
	public function getQuote();

	/**
	 * @return array
	 */
	public function getParameters();

	/**
	 * @return boolean
	 */
	public function isMoto();
}