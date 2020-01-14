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

namespace Customweb\Base\Plugin\Checkout\Block;

class Cart
{
	/**
	 * Core registry
	 *
	 * @var \Magento\Framework\Registry
	 */
	protected $_coreRegistry = null;

	/**
	 * @var \Customweb\Base\Model\ExternalCheckout\Widget\Collection
	 */
	protected $_widgetCollection;

	/**
	 * @param \Magento\Framework\Registry $coreRegistry
	 * @param \Customweb\Base\Model\ExternalCheckout\Widget\Collection $widgetCollection
	 */
	public function __construct(
			\Magento\Framework\Registry $coreRegistry,
			\Customweb\Base\Model\ExternalCheckout\Widget\Collection $widgetCollection
	) {
		$this->_coreRegistry = $coreRegistry;
		$this->_widgetCollection = $widgetCollection;
	}

	public function beforeGetMethods(\Magento\Checkout\Block\Cart $subject, $alias)
	{
		if ($alias == 'methods') {
			foreach ($this->_widgetCollection->getWidgets() as $widget) {
				$block = $subject->getLayout()->addBlock($widget->getBlockClass(), 'cart.customweb.external_checkout.widget.' . $widget->getCheckoutName(), 'checkout.cart.methods');
				$block->setWidget($widget);
			}
		}
	}
}