<?php
namespace Magesales\Shippingtable\Block\Adminhtml\Method\Edit;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form as WidgetForm;
use Magento\Framework\Data\FormFactory;

class Form extends WidgetForm
{
    protected $formFactory;
	
	/**
     * {@inheritdoc}
     * @param FormFactory $formFactory
     * @param Context     $context
     */
    public function __construct(
        FormFactory $formFactory,
        Context $context
    ) {
        $this->formFactory = $formFactory;
		parent::__construct($context);
    }
	
	protected function _prepareForm()
    {
        $form = $this->formFactory->create(
            ['data' => ['id' => 'edit_form', 
						'action' => $this->getUrl('*/*/save', ['id' => $this->getRequest()->getParam('id')]),
						'method' => 'post', 
						'enctype' => 'multipart/form-data']]
			);
		
		$form->setUseContainer(true);
        $this->setForm($form);
        
        return parent::_prepareForm();
    }
}