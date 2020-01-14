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
 * @copyright  Copyright (c) 2017 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */
namespace Ves\Megamenu\Model\Config\Source;
class ListCmsPage implements \Magento\Framework\Option\ArrayInterface
{
	protected  $_blockModel;

    /**
     * @param \Magento\Cms\Model\Page $blockModel
     */
    public function __construct(
    	\Magento\Cms\Model\Page $blockModel
    	) {
    	$this->_groupModel = $blockModel;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
    	$collection = $this->_groupModel->getCollection();
    	$blocks = [];
        $blocks[] = ['value' => '', 'label' => __('-- Please Select CMS Page --')];
    	foreach ($collection as $_block) {
    		$blocks[] = [
    		'value' => $_block->getIdentifier(),
    		'label' => addslashes($_block->getTitle())
    		];
    	}
        return $blocks;
    }
}