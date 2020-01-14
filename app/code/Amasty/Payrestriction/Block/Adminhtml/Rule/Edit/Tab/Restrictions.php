<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Payrestriction
 */


namespace Amasty\Payrestriction\Block\Adminhtml\Rule\Edit\Tab;

use Amasty\Payrestriction\Model\RegistryConstants;
use Amasty\CommonRules\Block\Adminhtml\Rule\Edit\Tab\General as CommonRulesGeneral;

class Restrictions extends CommonRulesGeneral
{
    public function _construct()
    {
        $this->setRegistryKey(RegistryConstants::REGISTRY_KEY);
        parent::_construct();
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Restrictions');
    }

    protected function formInit($model)
    {
        $form = parent::formInit($model);

        $fieldset = $form->getElement('apply_in');
        $fieldset->addField(
            'methods',
            'multiselect',
            [
                'name' => 'methods[]',
                'label' => __('Methods'),
                'values' => $this->poolOptionProvider->getOptionsByProviderCode('payment_method'),
                'required' => true
            ]
        );

        return $form;
    }
}
