<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CommonRules
 */


namespace Amasty\CommonRules\Model\Rule\Condition;

class Address extends \Magento\SalesRule\Model\Rule\Condition\Address
{
    /**
     * Address constructor.
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param \Magento\Directory\Model\Config\Source\Country $directoryCountry
     * @param \Magento\Directory\Model\Config\Source\Allregion $directoryAllregion
     * @param \Magento\Shipping\Model\Config\Source\Allmethods $shippingAllmethods
     * @param \Magento\Payment\Model\Config\Source\Allmethods $paymentAllmethods
     * @param array $data
     */
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\Directory\Model\Config\Source\Country $directoryCountry,
        \Magento\Directory\Model\Config\Source\Allregion $directoryAllregion,
        \Magento\Shipping\Model\Config\Source\Allmethods $shippingAllmethods,
        \Magento\Payment\Model\Config\Source\Allmethods $paymentAllmethods,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $directoryCountry,
            $directoryAllregion,
            $shippingAllmethods,
            $paymentAllmethods,
            $data
        );
    }

    /**
     * @return $this
     */
    public function loadAttributeOptions()
    {
        parent::loadAttributeOptions();
        $this->setAttributeOption($this->getAttributeOption());

        return $this;
    }

    /**
     * @return string
     */
    public function getInputType()
    {
        switch ($this->getAttribute()) {
            case 'package_value':
            case 'package_weight':
            case 'package_qty':
            case 'base_subtotal':
            case 'weight':
            case 'total_qty':
                return 'numeric';

            case 'dest_country_id':
            case 'dest_region_id':
            case 'shipping_method':
            case 'payment_method':
            case 'country_id':
            case 'region_id':
                return 'select';
        }

        return 'string';
    }

    /**
     * @return string
     */
    public function getValueElementType()
    {
        switch ($this->getAttribute()) {
            case 'dest_country_id':
            case 'dest_region_id':
            case 'shipping_method':
            case 'payment_method':
            case 'country_id':
            case 'region_id':
                return 'select';
        }

        return 'text';
    }

    /**
     * @return mixed
     */
    public function getValueSelectOptions()
    {
        if (!$this->hasData('value_select_options')) {
            switch ($this->getAttribute()) {
                case 'country_id':
                case 'dest_country_id':
                    $options = $this->_directoryCountry->toOptionArray();
                    break;

                case 'region_id':
                case 'dest_region_id':
                    $options = $this->_directoryAllregion->toOptionArray();
                    break;

                case 'shipping_method':
                    $options = $this->_shippingAllmethods->toOptionArray();
                    break;

                case 'payment_method':
                    $options = $this->_paymentAllmethods->toOptionArray();
                    break;

                default:
                    $options = array();
            }
            $this->setData('value_select_options', $options);
        }
        return $this->getData('value_select_options');
    }

    /**
     * @return array
     */
    public function getOperatorSelectOptions()
    {
        $operators = $this->getOperatorOption();
        if ($this->getAttribute() == 'dest_street') {
            $operators = [
                '{}'  => __('contains'),
                '!{}' => __('does not contain'),
                '{%'  => __('starts from'),
                '%}'  => __('ends with'),
            ];
        }

        if ($this->getAttribute() == 'street') {
            $operators = array(
                '{}'  => __('contains'),
                '!{}' => __('does not contain'),
            );
        }

        $type = $this->getInputType();
        $opt = [];
        $operatorByType = $this->getOperatorByInputType();

        foreach ($operators as $k => $v) {
            if (!$operatorByType || in_array($k, $operatorByType[$type])) {
                $opt[] = [
                    'value' => $k,
                    'label' => $v
                ];
            }
        }

        return $opt;
    }

    /**
     * @return array
     */
    public function getDefaultOperatorInputByType()
    {
        $operators = parent::getDefaultOperatorInputByType();
        $operators['string'][] = '{%';
        $operators['string'][] = '%}';

        return $operators;
    }

    /**
     * @return array
     */
    public function getDefaultOperatorOptions()
    {
        $operators = parent::getDefaultOperatorOptions();
        $operators['{%'] = __('starts from');
        $operators['%}'] = __('ends with');

        return $operators;
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $model
     * @return bool
     */
    public function validate(\Magento\Framework\Model\AbstractModel $model)
    {
        if ($model instanceof \Magento\Quote\Model\Quote\Address) {
            $quote = $model->getQuote();
            $attributeName = $this->getQuoteAttributeName($this->getAttribute(), $quote);

            if ($quote->isVirtual()) {
                $model = $quote;
            }
        }

        if (!$model->hasData($attributeName)) {
            $model->load($model->getId());
        }

        $attributeValue = $model->getData($attributeName);

        return $this->validateAttribute($attributeValue);
    }

    /**
     * @param $conditionAttribute
     * @param \Magento\Quote\Model\Quote $quote
     * @return string
     */
    private function getQuoteAttributeName($conditionAttribute, $quote)
    {
        switch ($conditionAttribute) {
            case 'total_qty':
                return $quote->isVirtual() ? 'items_qty' : 'total_qty';
            default:
                return $conditionAttribute;
        }
    }

    /**
     * @param array|bool|float|int|object|string $validatedValue
     * @return bool
     */
    public function validateAttribute($validatedValue)
    {
        if (is_object($validatedValue)) {
            return false;
        }

        if (is_string($validatedValue)){
            $validatedValue = strtoupper($validatedValue);
        }

        /**
         * Condition attribute value
         */
        $value = $this->getValueParsed();
        if (is_string($value)){
            $value = strtoupper($value);
        }

        /**
         * Comparison operator
         */
        $op = $this->getOperatorForValidate();

        // if operator requires array and it is not, or on opposite, return false
        if ($this->isArrayOperatorType() xor is_array($value)) {
            return false;
        }

        $result = false;
        switch ($op) {
            case '{%':
                if (!is_scalar($validatedValue)) {
                    return false;
                } else {
                    $result = substr($validatedValue,0,strlen($value)) == $value;
                }
                break;
            case '%}':
                if (!is_scalar($validatedValue)) {
                    return false;
                } else {
                    $result = substr($validatedValue,-strlen($value)) == $value;
                }
                break;
            default:
                return parent::validateAttribute($validatedValue);
                break;
        }

        return $result;
    }
}