<?php
namespace Magesales\Shippingtable\Block\Adminhtml\Method\Edit\Tab;
use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Magento\Store\Model\System\Store as SystemStore;

class Import extends Form
{
	/**
     * @var SystemStore
     */
    protected $systemStore;
	/**
     * @var FormFactory
     */
    protected $formFactory;
	/**
     * @var Registry
     */
    protected $registry;
	/**
     * @var Context
     */
    protected $context;
	
	/**
     * {@inheritdoc}
     * @param SourceType  $sourceType
     * @param SystemStore $systemStore
     * @param FormFactory $formFactory
     * @param Registry    $registry
     * @param Context     $context
     */
    public function __construct(
        SystemStore $systemStore,
        FormFactory $formFactory,
        Registry $registry,
        Context $context
	) {
        $this->systemStore = $systemStore;
        $this->formFactory = $formFactory;
        $this->registry = $registry;
        $this->context = $context;
        parent::__construct($context);
    }
	
    protected function _prepareForm()
    {
        //create form structure
        $form = $this->formFactory->create();
        $this->setForm($form);
        
        $fldSet = $form->addFieldset('shippingtable_import', ['legend'=> __('Import Rates')]);
        $fldSet->addField('import_clear', 'select', [
          'label'     => __('Delete Existing Rates'),
          'name'      => 'import_clear',
          'values'    => [
            [
                'value' => 0,
                'label' => __('No')
            ],
            [
                'value' => 1,
                'label' => __('Yes')
            ]]
        ]);
		
        $fldSet->addField('import_file', 'file', [
          'label'     => __('CSV File'),
          'name'      => 'import_file'
        ]);               

        return parent::_prepareForm();
    }
}