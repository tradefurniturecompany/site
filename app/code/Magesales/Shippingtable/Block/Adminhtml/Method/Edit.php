<?php
namespace Magesales\Shippingtable\Block\Adminhtml\Method;
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
	
	public function _construct()
    {
        parent::_construct();
                 
        $this->_objectId = 'id'; 
        $this->_blockGroup = 'Magesales_Shippingtable';
        $this->_controller = 'adminhtml_method';
        
        /*$this->buttonList->add('save_and_continue', array(
                'label'     => __('Save and Continue Edit'),
                'onclick'   => 'saveAndContinueEdit()',
                'class' => 'save'
            ), 10);*/
       
        /*$mid = $this->_coreRegistry->registry('shippingtable_method')->getId();
		if ($mid)
		{
            $this->buttonList->add('new', array(
                    'label' => __('Add New Rate'),
                    'onclick' => 'newRate()',
                    'class' => 'add'
                ),15);

            $url = $this->getUrl('shippingtable/rate/edit', ['mid'=>$mid]);  
            $this->_formScripts[] = " function newRate(){ setLocation('$url'); } ";    
        }    
        $this->_formScripts[] = " function saveAndContinueEdit(){ editForm.submit($('edit_form').action + 'continue/edit') }";   */  
		
            $this->buttonList->add(
                'saveandcontinue',
                [
                    'label' => __('Save and Continue Edit'),
                    'class' => 'save',
                    'data_attribute' => [
                        'mage-init' => [
                            'button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form'],
                        ],
                    ]
                ],
                -100
            );  
    }

    public function getHeaderText()
    {
        $header = __('New Method');
        $model = $this->_coreRegistry->registry('shippingtable_method');
        if ($model->getId()){
            $header = __('Edit Method `%1`', $model->getName());
        }
        return $header;
    }
	
	protected function _getSaveAndContinueUrl()
    {
        return $this->getUrl('shippingtable/method/save', ['_current' => true, 'back' => 'edit', 'active_tab' => '{{tab_id}}']);
    }
}