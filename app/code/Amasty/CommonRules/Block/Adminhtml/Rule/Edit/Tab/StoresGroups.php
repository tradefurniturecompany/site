<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CommonRules
 */


namespace Amasty\CommonRules\Block\Adminhtml\Rule\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;

class StoresGroups extends AbstractTab
{

    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $systemStore;

    /**
     * @var \Magento\Config\Model\Config\Source\Yesno
     */
    private $yesno;

    /**
     * StoresGroups constructor.
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param \Amasty\CommonRules\Model\OptionProvider\Pool $poolOptionProvider
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param \Magento\Config\Model\Config\Source\Yesno $yesno
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        \Amasty\CommonRules\Model\OptionProvider\Pool $poolOptionProvider,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Config\Model\Config\Source\Yesno $yesno,
        array $data = []
    ) {
        $this->yesno = $yesno;
        $this->systemStore = $systemStore;
        parent::__construct($context, $registry, $formFactory, $poolOptionProvider, $data);
    }

    /**
     * Prepare form before rendering HTML
     */
    protected function _prepareForm()
    {
        $model = $this->getModel();
        $form = $this->formInit($model);
        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    protected  function getLabel()
    {
        return __('Stores & Customer Groups');
    }

    /**
     * @inheritdoc
     */
    protected function formInit($model)
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $fldStore = $form->addFieldset('apply_in', ['legend' => __('Apply In')]);

        $fldStore->addField(
            'for_admin',
            'select',
            [
                'name'      => 'for_admin',
                'label'     => __('Admin Area'),
                'values'   => $this->yesno->toArray()
            ]
        );

        $fldStore->addField(
            'stores',
            'multiselect',
            [
                'name'      => 'stores[]',
                'label'     => __('Stores'),
                'values' => $this->systemStore->getStoreValuesForForm(false, false),
                'note'      => __('Leave empty or select all to apply the rule to any store'),
            ]
        );

        $fldCust = $form->addFieldset('apply_for', ['legend'=> __('Apply For')]);
        $fldCust->addField(
            'cust_groups',
            'multiselect',
            [
                'name'      => 'cust_groups[]',
                'label'     => __('Customer Groups'),
                'values'    => $this->poolOptionProvider->getOptionsByProviderCode('customer_group'),
                'note'      => __('Leave empty or select all to apply the rule to any group'),
            ]
        );

        return $form;
    }
}
