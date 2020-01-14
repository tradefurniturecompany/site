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

namespace Customweb\RealexCw\Model\ExternalCheckout;

class Widget implements \Customweb\Base\Model\ExternalCheckout\IWidget
{
	/**
	 * @var \Customweb_Payment_ExternalCheckout_ICheckout
	 */
	private $checkout;

	/**
	 * @var string
	 */
	private $html;

	/**
	 * @param \Customweb_Payment_ExternalCheckout_ICheckout $checkout
	 * @param string $widgetHtml
	 */
	public function __construct(\Customweb_Payment_ExternalCheckout_ICheckout $checkout, $widgetHtml)
	{
		$this->checkout = $checkout;
		$this->html = $widgetHtml;
	}

	public function getCheckoutName()
	{
		return $this->checkout->getMachineName();
	}

	public function getSortOrder()
	{
		return $this->checkout->getSortOrder();
	}

	public function getHtml()
	{
		return $this->html;
	}

	public function getBlockClass()
	{
		return 'Customweb\RealexCw\Block\ExternalCheckout\Widget';
	}
}