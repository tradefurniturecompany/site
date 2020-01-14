<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CommonRules
 */


namespace Amasty\CommonRules\Model\Rule\Condition\Total;

use Magento\Rule\Model\Condition as Condition;

class Period extends \Magento\Rule\Model\Condition\AbstractCondition
{

    /**
     * Period constructor.
     * @param Condition\Context $context
     * @param array $data
     */
    public function __construct(
        Condition\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * @return $this
     */
    public function loadAttributeOptions()
    {
        $attributes = [
            'period' => __('Period after order was placed'),
        ];

        $this->setAttributeOption($attributes);

        return $this;
    }

    /**
     * @return $this
     */
    public function loadOperatorOptions()
    {
        $this->setOperatorOption(
            [
                '>=' => __('equals or less than'),
                '<=' => __('equals or greater than'),
                '>'  => __('less than'),
                '<'  => __('greater than'),
                '='  => __('is'),
            ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    public function getAttributeElement()
    {
        $element = parent::getAttributeElement();
        $element->setShowAsText(true);

        return $element;
    }

    /**
     * @return string
     */
    public function getInputType()
    {
        return 'numeric';
    }

    /**
     * @return string
     */
    public function getValueElementType()
    {
        return 'text';
    }

    /**
     * @return mixed
     */
    public function getValueSelectOptions()
    {
        $options = [];

        $key = 'value_select_options';
        if (!$this->hasData($key)) {
            $this->setData($key, $options);
        }

        return $this->getData($key);
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $model
     * @return array
     */
    public function validate(\Magento\Framework\Model\AbstractModel $model)
    {
        $v = min(16000, $this->getValue());

        $date   = date("Y-m-d H:i:s", time() - $v * 24 * 3600);
        $result = ['date' => $this->getOperatorForValidate() . "'" . $date . "'"];

        return $result;
    }
}
