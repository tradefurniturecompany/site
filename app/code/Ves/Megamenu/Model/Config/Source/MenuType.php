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
use Magento\Framework\Data\OptionSourceInterface;

class MenuType implements OptionSourceInterface
{
    public function toOptionArray()
    {
        $options = [];
        $options[] = [
                'label' => __('Child Menu Item'),
                'value' => 'childmenu',
        ];
        $options[] = [
                'label' => __('Content'),
                'value' => 'content',
        ];
        $options[] = [
                'label' => __('Dynamic Content Tab'),
                'value' => 'dynamic',
        ];
        $options[] = [
                'label' => __('Sub-Categories'),
                'value' => 'parentcat',
        ];
        return $options;
    }
}
