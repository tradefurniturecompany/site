<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CommonRules
 */


namespace Amasty\CommonRules\Model\Rule\Condition;

class Customer extends \Magento\Rule\Model\Condition\AbstractCondition
{
    const AMASTY_CUSTOMER_ATTRIBUTES_KEY_NAME = 'custom_attributes';

    /**
     * @var \Magento\Customer\Model\ResourceModel\Customer
     */
    private $customerResource;

    /**
     * @var \Magento\Config\Model\Config\Source\Yesno
     */
    private $yesnoOptions;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    private $customerFactory;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var array
     */
    protected $notAllowedAttributes = [
        'lock_expires', 'first_failure', 'group_id', 'default_billing', 'default_shipping', 'failures_num'
    ];

    /**
     * @var array
     */
    protected $restrictedFrontendInputs = ['statictext', 'textarea', 'file'];

    /**
     * Customer constructor.
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param \Magento\Customer\Model\ResourceModel\Customer $customerResource
     * @param array $data
     */
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\Customer\Model\ResourceModel\Customer $customerResource,
        \Magento\Config\Model\Config\Source\Yesno $yesnoOptions,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        array $data = []
    ) {
        $this->customerResource = $customerResource;
        $this->yesnoOptions = $yesnoOptions;
        $this->customerFactory = $customerFactory;
        $this->customerRepository = $customerRepository;
        parent::__construct($context, $data);
    }

    /**
     * @return $this
     */
    public function loadAttributeOptions()
    {
        $customerAttributes = $this->customerResource->loadAllAttributes()->getAttributesByCode();
        $attributes = [];

        foreach ($customerAttributes as $attribute) {

            if (!$this->isCanShow($attribute)) {
                continue;
            }

            $attributes[$attribute->getAttributeCode()] = $attribute->getFrontendLabel();
        }

        asort($attributes);
        $this->setAttributeOption($attributes);

        return $this;
    }

    /**
     * @param $attribute
     * @return bool
     */
    protected function isCanShow($attribute)
    {
        $isAllowedAttribute = !in_array($attribute->getAttributeCode(), $this->notAllowedAttributes);
        $isExist = !(!$attribute->getFrontendLabel() || !$attribute->getAttributeCode());
        $isRestrictedFrontendInput = !in_array($attribute->getFrontendInput(), $this->restrictedFrontendInputs);

        return $isAllowedAttribute && $isExist && $isRestrictedFrontendInput;
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
     * This value will define which operators will be available for this condition.
     *
     * Possible values are: string, numeric, date, select, multiselect, grid, boolean
     *
     * @return string
     */
    public function getInputType()
    {
        $customerAttribute = $this->getAttributeObject();

        switch ($customerAttribute->getFrontendInput()) {
            case 'boolean':
                return 'select';
            case 'text':
                return 'string';
            case 'datetime':
                return 'date';
            default:
                return $customerAttribute->getFrontendInput();
        }
    }

    /**
     * @return $this
     */
    public function getValueElement()
    {
        $element = parent::getValueElement();

        switch ($this->getInputType()) {
            case 'date':
                $element->setClass('hasDatepicker');
                break;
        }

        return $element;
    }

    /**
     * @return bool
     */
    public function getExplicitApply()
    {
        return ($this->getInputType() == 'date');
    }

    /**
     * Value element type will define renderer for condition value element
     *
     * @see \Magento\Framework\Data\Form\Element
     * @return string
     */
    public function getValueElementType()
    {
        $customerAttribute = $this->getAttributeObject();

        switch ($customerAttribute->getFrontendInput()) {
            case 'boolean':
                return 'select';
            default:
                return $customerAttribute->getFrontendInput();
        }
    }

    /**
     * @return array
     */
    public function getValueSelectOptions()
    {
        $selectOptions = [];
        $attributeObject = $this->getAttributeObject();

        if (is_object($attributeObject) && $attributeObject->usesSource()) {
            $addEmptyOption = !$attributeObject->getFrontendInput() == 'multiselect';
            $selectOptions = $attributeObject->getSource()->getAllOptions($addEmptyOption);
        }

        if ($this->getInputType() == 'boolean' && count($selectOptions) == 0) {
            $selectOptions = $this->yesnoOptions->toOptionArray();
        }

        $key = 'value_select_options';

        if (!$this->hasData($key)) {
            $this->setData($key, $selectOptions);
        }

        return $this->getData($key);
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return bool
     */
    public function validate(\Magento\Framework\Model\AbstractModel $object)
    {
        $customer = $object;
        $attr = $this->getAttribute();
        if (!$customer instanceof \Magento\Customer\Model\Customer) {
            $customer = $object->getQuote()->getCustomer();
            $customerData = $customer->__toArray();

            if ($attr == 'confirmation') {
                if (array_key_exists($attr, $customerData)) {
                    $customerData[$attr] = (int)$customerData[$attr] == null;
                }
            }
            if (!isset($customerData[$attr])) {
                if (isset($customerData[self::AMASTY_CUSTOMER_ATTRIBUTES_KEY_NAME])) {
                    return $this->validateCustomerAttributes(
                        $attr,
                        $customerData[self::AMASTY_CUSTOMER_ATTRIBUTES_KEY_NAME]
                    );
                }

                return false;
            }

            return parent::validateAttribute($customerData[$attr]);
        }

        return parent::validate($customer);
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getConditionLabel()
    {
        return __('Customer attributes');
    }

    /**
     * @return false|\Magento\Eav\Model\Entity\Attribute\AbstractAttribute
     */
    protected function getAttributeObject()
    {
        return $this->customerResource
            ->getAttribute($this->getAttribute());
    }

    /**
     * @param $attribute
     * @param array $customerAttributes
     * @return bool
     */
    protected function validateCustomerAttributes($attribute, array $customerAttributes)
    {
        if (!isset($customerAttributes[$attribute]) || !isset($customerAttributes[$attribute]['value'])) {
            return false;
        }

        return parent::validateAttribute($customerAttributes[$attribute]['value']);
    }
}

