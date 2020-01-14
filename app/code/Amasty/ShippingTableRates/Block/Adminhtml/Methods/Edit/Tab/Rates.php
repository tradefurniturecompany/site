<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingTableRates
 */


namespace Amasty\ShippingTableRates\Block\Adminhtml\Methods\Edit\Tab;

use Magento\Backend\Block\Widget\Tab\TabInterface;

/**
 * Shipping Rates Tab
 */
class Rates extends \Magento\Backend\Block\Widget\Grid\Container implements TabInterface
{
    /**
     * @var \Amasty\ShippingTableRates\Model\Method $_model
     */
    protected $_model;

    public function getTabLabel()
    {
        return __('Methods and Rates');
    }

    public function getTabTitle()
    {
        return __('Methods and Rates');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data
    ) {
        $this->_model = $registry->registry('current_amasty_table_method');
        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        $this->_controller = 'adminhtmlMethods';
        $this->_headerText = __('Rates');

        if ($this->_model->getId()) {
            $this->_addButtonLabel = __('Add New Rate');
            $this->addButton(
                'add_new',
                [
                    'label' => $this->getAddButtonLabel(),
                    'onclick' => 'setLocation(\'' . $this->getCreateUrl() . '\')',
                    'class' => 'add primary'
                ],
                0,
                0,
                $this->getNameInLayout()
            );
        }

        $this->removeButton('add');
    }

    public function getCreateUrl()
    {
        return $this->getUrl('*/rates/newAction', ['method_id' => $this->_model->getId()]);
    }
}
