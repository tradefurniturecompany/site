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

namespace Customweb\Base\Model\ExternalCheckout\Widget;

class Collection
{
	/**
	 * Core event manager proxy
	 *
	 * @var \Magento\Framework\Event\ManagerInterface
	 */
	protected $_eventManager = null;

	/**
	 * @var \Customweb\Base\Model\ExternalCheckout\IWidget[]
	 */
	private $widgets = null;

	/**
	 * @param \Magento\Framework\Event\ManagerInterface $eventManager
	 */
	public function __construct(
			\Magento\Framework\Event\ManagerInterface $eventManager
	) {
		$this->_eventManager = $eventManager;
	}

	/**
	 * @return \Customweb\Base\Model\ExternalCheckout\IWidget[]
	 */
	public function getWidgets()
	{
		if ($this->widgets == null) {
			$this->collectWidgets();
		}
		return $this->widgets;
	}

	/**
	 * @param \Customweb\Base\Model\ExternalCheckout\IWidget $widget
	 * @return \Customweb\Base\Model\ExternalCheckout\Widget\Collection
	 */
	public function addWidget(\Customweb\Base\Model\ExternalCheckout\IWidget $widget)
	{
		$this->widgets[] = $widget;
		return $this;
	}

	private function collectWidgets()
	{
		$this->widgets = [];
		$this->_eventManager->dispatch(
				'customweb_externalcheckout_widgets_collect',
				['collection' => $this]
		);
		usort($this->widgets, function($a, $b){
			if ($a->getSortOrder() == $b->getSortOrder()) {
				return 0;
			} else {
				return $a->getSortOrder() < $b->getSortOrder() ? -1 : 1;
			}
		});
	}
}