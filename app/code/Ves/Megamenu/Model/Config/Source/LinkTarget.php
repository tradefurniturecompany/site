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

class LinkTarget
{
    public function toOptionArray()
    {
        $options = [];
        $options[] = [
            'label' => __('Load in a new window'),
            'value' => '_blank'
        ];
        $options[] = [
            'label' => __('Load in the same frame as it was clicked'),
            'value' => '_self'
        ];
        $options[] = [
            'label' => __('Load in the parent frameset'),
            'value' => '_parent'
        ];
        $options[] = [
            'label' => __('Load in the full body of the window'),
            'value' => '_top'
        ];
        return $options;
    }
}