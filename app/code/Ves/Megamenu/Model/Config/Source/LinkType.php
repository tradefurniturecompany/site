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

class LinkType
{
    public function toOptionArray()
    {
        $options = [];
        $options[] = [
            'label' => __('Custom Link'),
            'value' => 'custom_link'
        ];
        $options[] = [
            'label' => __('Category Link'),
            'value' => 'category_link'
        ];
        /*$options[] = [
            'label' => __('None'),
            'value' => 'none'
        ];*/
        return $options;
    }
}
