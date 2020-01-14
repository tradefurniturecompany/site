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

namespace Customweb\Base\Block\ExternalCheckout;

class AbstractWidget extends \Magento\Framework\View\Element\Template
{
	/**
	 * @var \Customweb\Base\Model\ExternalCheckout\IWidget
	 */
	private $widget;

	/**
	 * @return \Customweb\Base\Model\ExternalCheckout\IWidget
	 */
	public function getWidget()
	{
		if (!($this->widget instanceof \Customweb\Base\Model\ExternalCheckout\IWidget)) {
			throw new \Exception('The widget has not been set.');
		}
		return $this->widget;
	}

	/**
	 * @param \Customweb\Base\Model\ExternalCheckout\IWidget $widget
	 * @return \Customweb\Base\Block\ExternalCheckout\AbstractWidget
	 */
	public function setWidget(\Customweb\Base\Model\ExternalCheckout\IWidget $widget)
	{
		$this->widget = $widget;
		return $this;
	}
}