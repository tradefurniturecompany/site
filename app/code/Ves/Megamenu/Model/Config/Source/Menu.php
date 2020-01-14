<?php
/**
 * Venustheme
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Venustheme.com license that is
 * available through the world-wide-web at this URL:
 * http://www.venustheme.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Venustheme
 * @package    Ves_Megamenu
 * @copyright  Copyright (c) 2016 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */
namespace Ves\Megamenu\Model\Config\Source;

class Menu implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Ves\Megamenu\Model\Menu
     */
	protected  $_menu;

    public function __construct(
    	\Ves\Megamenu\Model\Menu $menu
    	) {
    	$this->_menu = $menu;
    }

	public function toOptionArray()
	{
		$data = [];
		$collection = $this->_menu->getCollection();
		foreach ($collection as $menu) {
			$data[] = [
				'value' => $menu->getAlias(),
				'label' => $menu->getName()
			];
		}
		return $data;
	}

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
    	return [0 => __('No'), 1 => __('Yes')];
    }
}