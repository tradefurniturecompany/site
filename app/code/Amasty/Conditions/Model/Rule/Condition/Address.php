<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Conditions
 */


namespace Amasty\Conditions\Model\Rule\Condition;

use Amasty\Conditions\Api\Data\AddressInterface;
use Amasty\Conditions\Model\Constants;

class Address extends \Magento\Rule\Model\Condition\AbstractCondition
{
    const CUSTOM_OPERATORS = [
        AddressInterface::SHIPPING_ADDRESS_LINE,
        AddressInterface::CITY,
    ];

    /**
     * @var \Magento\Directory\Model\Config\Source\Country
     */
    private $country;

    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * @var \Magento\Payment\Model\Config\Source\Allmethods
     */
    private $allMethods;

    /**
     * @var \Amasty\Conditions\Model\Address
     */
    private $address;

    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\Directory\Model\Config\Source\Country $country,
        \Magento\Payment\Model\Config\Source\Allmethods $allMethods,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        \Amasty\Conditions\Model\Address $address,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->productMetadata = $productMetadata;
        $this->country = $country;
        $this->allMethods = $allMethods;
        $this->address = $address;
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function loadAttributeOptions()
    {
        $attributes = [
            AddressInterface::BILLING_ADDRESS_COUNTRY => __('Billing Address Country'),
            AddressInterface::PAYMENT_METHOD => __('Payment Method'),
            AddressInterface::SHIPPING_ADDRESS_LINE => __('Shipping Address Line'),
            AddressInterface::CITY => __('City'),
        ];
        $this->setAttributeOption($attributes);

        return $this;
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getOperatorSelectOptions()
    {
        if (in_array($this->getAttribute(), self::CUSTOM_OPERATORS)) {
            $operators = $this->getOperators();
            $type = $this->getInputType();
            $result = [];
            $operatorByType = $this->getOperatorByInputType();
            foreach ($operators as $operatorKey => $operatorValue) {
                if (!$operatorByType || in_array($operatorKey, $operatorByType[$type])) {
                    $result[] = ['value' => $operatorKey, 'label' => $operatorValue];
                }
            }

            return $result;
        }

        return parent::getOperatorSelectOptions();
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getOperators()
    {
        if ($this->getAttribute() === AddressInterface::SHIPPING_ADDRESS_LINE) {
            return [
                '{}' => __('contains'),
                '!{}' => __('does not contain'),
            ];
        } elseif ($this->getAttribute() === AddressInterface::CITY) {
            return [
                '{}' => __('contains'),
                '!{}' => __('does not contain'),
                '==' => __('is'),
                '!=' => __('is not'),
                '()' => __('is one of'),
                '!()' => __('is not one of'),
            ];
        }

        return [];
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getAttributeElement()
    {
        $element = parent::getAttributeElement();
        $element->setShowAsText(true);

        return $element;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getInputType()
    {
        return $this->getAttribute() === AddressInterface::SHIPPING_ADDRESS_LINE
        || $this->getAttribute() === AddressInterface::CITY
            ? 'string'
            : 'select';
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getValueElementType()
    {
        return $this->getAttribute() === AddressInterface::SHIPPING_ADDRESS_LINE
        || $this->getAttribute() === AddressInterface::CITY
            ? 'text'
            : 'select';
    }

    /**
     * @return array|mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getValueSelectOptions()
    {
        if (!$this->hasData(Constants::VALUE_SELECT_OPTIONS)) {
            switch ($this->getAttribute()) {
                case AddressInterface::BILLING_ADDRESS_COUNTRY:
                    $options = $this->country->toOptionArray();
                    break;

                case AddressInterface::PAYMENT_METHOD:
                    $options = $this->allMethods->toOptionArray();
                    break;

                default:
                    $options = [];
            }
            $this->setData(Constants::VALUE_SELECT_OPTIONS, $options);
        }

        return $this->getData(Constants::VALUE_SELECT_OPTIONS);
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $model
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function validate(\Magento\Framework\Model\AbstractModel $model)
    {
        $address = $model;
        if (!$address instanceof \Magento\Quote\Model\Quote\Address) {
            $address = $address->getQuote()->isVirtual()
                ? $address->getQuote()->getBillingAddress()
                : $address->getQuote()->getShippingAddress();
        }

        $attrValue = $this->getAttributeValue($address);
        if (!$attrValue) {
            $attrValue = $this->getDefaultAttrValue($address);
        }

        return parent::validateAttribute($attrValue);
    }

    /**
     * @param \Magento\Quote\Model\Quote\Address $address
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getDefaultAttrValue(\Magento\Quote\Model\Quote\Address $address)
    {
        $attrValue = null;
        switch ($this->getAttribute()) {
            case AddressInterface::BILLING_ADDRESS_COUNTRY:
                $attrValue = $address->getCountryId();
                break;

            case AddressInterface::PAYMENT_METHOD:
                $attrValue = $address->getPaymentMethod();
                break;

            case AddressInterface::SHIPPING_ADDRESS_LINE:
                $attrValue = $address->getStreetFull();
                break;

            case AddressInterface::CITY:
                $attrValue = $address->getCity();
                break;
        }

        return $attrValue;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Address $address
     * @return int|mixed|null|string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getAttributeValue(\Magento\Quote\Model\Quote\Address $address)
    {
        $attrValue = null;
        if ($this->address->isAdvancedConditions($address)) {
            $advConditions = $address->getExtensionAttributes()->getAdvancedConditions();
            switch ($this->getAttribute()) {
                case AddressInterface::BILLING_ADDRESS_COUNTRY:
                    $attrValue = $advConditions->getBillingAddressCountry();
                    break;

                case AddressInterface::PAYMENT_METHOD:
                    $attrValue = $advConditions->getPaymentMethod();
                    break;

                case AddressInterface::SHIPPING_ADDRESS_LINE:
                    $attrValue = $this->getStreetFull($advConditions->getAddressLine());
                    break;

                case AddressInterface::CITY:
                    $attrValue = $advConditions->getCity();
                    break;
            }
        }

        return $attrValue;
    }

    /**
     * @param $address
     * @return mixed|string
     */
    private function getStreetFull($address)
    {
        return is_array($address) ? implode("\n", $address) : $address;
    }
}
