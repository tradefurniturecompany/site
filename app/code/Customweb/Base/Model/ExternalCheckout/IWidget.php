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
 * @package		Customweb_Base
 */

namespace Customweb\Base\Model\ExternalCheckout;

interface IWidget
{
	/**
	 * Return the machine name of the checkout.
	 *
	 * @return string
	 */
	public function getCheckoutName();

	/**
	 * Returns an integer which indicates the sort order of the checkout.
	 * The returned number is used to determine the total order between
	 * all checkouts over all providers.
	 *
	 * @return int
	 */
	public function getSortOrder();

	/**
	 * Returns a HTML snippet which is used to provide a UI component for the
	 * customer to choose the checkout. By clicking on the checkout the checkout
	 * process with this checkout starts.
	 *
	 * @return string
	 */
	public function getHtml();

	/**
	 * Returns the class name of the block that renders the widget.
	 */
	public function getBlockClass();
}