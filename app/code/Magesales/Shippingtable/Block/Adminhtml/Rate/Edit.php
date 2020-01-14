<?php
namespace Magesales\Shippingtable\Block\Adminhtml\Rate;
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }
	
	protected function _construct()
    {
        parent::_construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'Magesales_Shippingtable';
        $this->_controller = 'adminhtml_rate';
        
        $this->buttonList->remove('back'); 
        $this->buttonList->remove('reset'); 
        $this->buttonList->remove('delete'); 
    }

    public function getHeaderText()
    {
        return __('Rate Configuration');
    }
}