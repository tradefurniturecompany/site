<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CommonRules
 */


namespace Amasty\CommonRules\Block\Adminhtml\Rule\Edit\Tab;

class Coupons extends AbstractTab
{
    /**
     * @return AbstractTab
     */
    protected function _prepareForm()
    {
        $model = $this->getModel();
        $form = $this->formInit($model);
        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    protected function getLabel()
    {
        return __('Cart Price Rules');
    }

    /**
     * Doing for possibility extend and additional new fields in tab form
     *
     * @param \Magento\Rule\Model\AbstractModel $model
     *
     * @return \Magento\Framework\Data\Form $form
     */
    protected function formInit($model)
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('rule_');

        $promoShippingRulesUrl = $this->getUrl('sales_rule/promo_quote');

        $fldInfo = $form->addFieldset('apply_restriction', ['legend' => __('Apply Rules Only With')]);

        if ($model->getId()) {
            $fldInfo->addField('rule_id', 'hidden', ['name' => 'rule_id']);
        }

        $fldInfo->addField(
            'coupon',
            'text',
            [
                'label' => __('Coupon Codes'),
                'name' => 'coupon',
                'note' => __(
                    'Apply this rule with provided coupons only. Comma separated. Create coupon in <a href="%1">'.'
<span>Promotions / Shopping Cart Rules</span></a> area first.',
                    $promoShippingRulesUrl
                ),
            ]
        );

        $fldInfo->addField(
            'discount_id',
            'multiselect',
            [
                'label'  => __('Shopping Cart Rule (discount)'),
                'name'   => 'discount_id[]',
                'values' => $this->poolOptionProvider->getOptionsByProviderCode('sales_rules'),
                'note'   => __(
                    'Apply this rule with ANY specified discount rules. Create rule in 
<a target="_blank" href="%1"><span>Promotions / Shopping Cart Price Rules</span></a> area first. 
Useful when you have MULTIPLE coupons in one rule',
                    $promoShippingRulesUrl
                ),
            ]
        );

        $fldInfo = $form->addFieldset('not_apply_restriction', ['legend'=> __('Do NOT Apply Rules With')]);
        $fldInfo->addField(
            'coupon_disable',
            'text',
            [
                'label' => __('Coupon codes'),
                'name' => 'coupon_disable',
                'note' => __(
                    'Not apply this rule with provided coupons only. Comma separated. Create coupon in
<a href="%1"><span>Promotions / Shopping Cart Rules</span></a> area first.',
                    $promoShippingRulesUrl
                ),
            ]
        );

        $fldInfo->addField(
            'discount_id_disable',
            'multiselect',
            [
                'label' => __('Shopping Cart Rule (discount)'),
                'name' => 'discount_id_disable[]',
                'values' => $this->poolOptionProvider->getOptionsByProviderCode('sales_rules'),
                'note' => __(
                    'Not apply this rule with ANY coupon from specified discount rules. Create rule in 
<a href="%1"><span>Promotions / Shopping Cart Price Rules</span></a> area first.
Useful when you have MULTIPLE coupons in one rule',
                    $promoShippingRulesUrl
                ),
            ]
        );

        return $form;
    }
}
