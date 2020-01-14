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

namespace Customweb\RealexCw\Observer\ExternalCheckout;

use Magento\Framework\Event\ObserverInterface;

class Widget implements ObserverInterface
{
	/**
	 * @var \Customweb\RealexCw\Model\ExternalCheckout\Widget\Collection
	 */
	protected $_widgetCollection;

	/**
	 * @param \Customweb\RealexCw\Model\ExternalCheckout\Widget\Collection $widgetCollection
	 */
	public function __construct(
			\Customweb\RealexCw\Model\ExternalCheckout\Widget\Collection $widgetCollection
	) {
		$this->_widgetCollection = $widgetCollection;
	}

	public function execute(\Magento\Framework\Event\Observer $observer)
	{
		/* @var $collection \Customweb\Base\Model\ExternalCheckout\Widget\Collection */
		$collection = $observer->getEvent()->getCollection();
		foreach ($this->_widgetCollection->getWidgets() as $widget) {
			$collection->addWidget($widget);
		}
	}
}