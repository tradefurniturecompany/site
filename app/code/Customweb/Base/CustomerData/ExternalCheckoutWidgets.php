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

namespace Customweb\Base\CustomerData;

class ExternalCheckoutWidgets extends \Magento\Framework\DataObject implements \Magento\Customer\CustomerData\SectionSourceInterface
{
	/**
	 * Core registry
	 *
	 * @var \Magento\Framework\Registry
	 */
	protected $_coreRegistry = null;

	/**
	 * @var \Magento\Framework\View\LayoutInterface
	 */
	protected $_layout;

	/**
	 * @var \Customweb\Base\Model\ExternalCheckout\Widget\Collection
	 */
	protected $_widgetCollection;

	/**
	 * @param \Magento\Framework\Registry $coreRegistry
	 * @param \Magento\Framework\View\LayoutInterface $layout
	 * @param \Customweb\Base\Model\ExternalCheckout\Widget\Collection $widgetCollection
	 * @param array $data
	 */
	public function __construct(
			\Magento\Framework\Registry $coreRegistry,
			\Magento\Framework\View\LayoutInterface $layout,
			\Customweb\Base\Model\ExternalCheckout\Widget\Collection $widgetCollection,
			array $data = []
	) {
		parent::__construct($data);
		$this->_coreRegistry = $coreRegistry;
		$this->_layout = $layout;
		$this->_widgetCollection = $widgetCollection;
	}

	public function getSectionData()
	{
		return [
			'html' => $this->getWidgetsHtml()
		];
	}

	/**
	 * @return array
	 */
	protected function getWidgetsHtml()
	{
		$data = [];
		foreach ($this->_widgetCollection->getWidgets() as $widget) {
			$data[] = $this->getWidgetHtml($widget);
		}
		return $data;
	}

	/**
	 * @param \Customweb\Base\Model\ExternalCheckout\IWidget $widget
	 * @return string
	 */
	protected function getWidgetHtml(\Customweb\Base\Model\ExternalCheckout\IWidget $widget)
	{
		/* @var $block \Customweb\Base\Block\ExternalCheckout\AbstractWidget */
		$block = $this->_layout->createBlock($widget->getBlockClass());
		$block->setWidget($widget);
		return $block->toHtml();
	}
}