<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingTableRates
 */


namespace Amasty\ShippingTableRates\Block\Adminhtml;

/**
 * Shipping Methods Grid Container
 */
class Methods extends \Magento\Backend\Block\Widget\Grid\Container
{
    public function _construct()
    {
        $this->_controller = 'adminhtml_methods';
        $this->_headerText = __('Shipping Table Rates');
        $this->_blockGroup = 'Amasty_ShippingTableRates';
        parent::_construct();
    }
}
