<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CommonRules
 */


namespace Amasty\CommonRules\Block\Adminhtml\Rule\Edit\Tab;

class Conditions extends AbstractTab
{
    const RULE_CONDITIONS_FIELDSET_NAMESPACE = 'rule_conditions_fieldset';

    /**
     * @var \Magento\Backend\Block\Widget\Form\Renderer\Fieldset
     */
    protected $rendererFieldset;

    /**
     * @var \Magento\Rule\Block\Conditions
     */
    protected $conditions;

    /**
     * @var \Magento\Framework\Data\FormFactory
     */
    protected $_formFactory;

    /**
     * Conditions constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Rule\Block\Conditions $conditions
     * @param \Magento\Backend\Block\Widget\Form\Renderer\Fieldset $rendererFieldset
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Amasty\CommonRules\Model\OptionProvider\Pool $poolOptionProvider,
        \Magento\Rule\Block\Conditions $conditions,
        \Magento\Backend\Block\Widget\Form\Renderer\Fieldset $rendererFieldset,
        array $data = []
    ) {
        $this->conditions = $conditions;
        $this->rendererFieldset = $rendererFieldset;
        $this->_formFactory = $formFactory;
        parent::__construct(
            $context,
            $registry,
            $formFactory,
            $poolOptionProvider,
            $data
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getLabel()
    {
        return __('Conditions');
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return $this
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        $model = $this->getModel();
        $form = $this->formInit($model);
        $form->setValues($model->getData());
        $form->addValues(['id' => $model->getId()]);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * @return mixed
     */
    protected function getModel()
    {
        return $this->_coreRegistry->registry($this->registryKey);
    }

    /**
     * @inheritdoc
     */
    protected function formInit($model)
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $renderer = $this->rendererFieldset->setTemplate(
            'Magento_CatalogRule::promo/fieldset.phtml'
        )->setNewChildUrl(
            $this->getUrl('amasty_rules/rule/newConditionHtml', ['form' => self::RULE_CONDITIONS_FIELDSET_NAMESPACE])
        );

        $fieldset = $form->addFieldset(
            self::RULE_CONDITIONS_FIELDSET_NAMESPACE,
            [
                'legend' => __(
                    'Apply the rule only if the following conditions are met (leave blank for all products).'
                )
            ]
        )->setRenderer(
            $renderer
        );

        $fieldset->addField(
            'conditions',
            'text',
            [
                'name' => 'conditions',
                'label' => __('Conditions'),
                'title' => __('Conditions')
            ]
        )->setRule(
            $model
        )->setRenderer(
            $this->conditions
        );

        $fieldAdvanced = $form->addFieldset(
            'advanced',
            [
                'legend'=> __('Backorders')
            ]
        );

        $fieldAdvanced->addField(
            'out_of_stock',
            'select',
            [
                'label' => __('Apply the rule to'),
                'name' => 'out_of_stock',
                'values' => $this->poolOptionProvider->getOptionsByProviderCode('backorders'),
            ]
        );

        $this->setConditionFormName($model->getConditions(), self::RULE_CONDITIONS_FIELDSET_NAMESPACE);

        return $form;
    }

    /**
     * @param \Magento\Rule\Model\Condition\AbstractCondition $conditions
     * @param string $formName
     * @return void
     */
    protected function setConditionFormName(
        \Magento\Rule\Model\Condition\AbstractCondition $conditions,
        $fieldsetName,
        $formName = null
    ) {
        if ($formName) {
            $conditions->setFormName($formName);
        }
        $conditions->setJsFormObject($fieldsetName);
        if ($conditions->getConditions() && is_array($conditions->getConditions())) {
            foreach ($conditions->getConditions() as $condition) {
                $this->setConditionFormName($condition, $fieldsetName, $formName);
            }
        }
    }
}
