<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Osc
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Osc\Helper;

use Exception;
use Magento\Customer\Helper\Address as CustomerAddressHelper;
use Magento\Customer\Model\Attribute;
use Magento\Customer\Model\AttributeMetadataDataProvider;
use Magento\Directory\Model\Region;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Locale\Resolver;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Address
 * @package Mageplaza\Osc\Helper
 */
class Address extends Data
{
    /** Field position */
    const SORTED_FIELD_POSITION = 'osc/field/position';

    /**
     * @type DirectoryList
     */
    protected $_directoryList;

    /**
     * @type Resolver
     */
    protected $_localeResolver;

    /**
     * @type Region
     */
    protected $_regionModel;

    /**
     * @var CustomerAddressHelper
     */
    protected $addressHelper;

    /**
     * @var AttributeMetadataDataProvider
     */
    private $attributeMetadataDataProvider;

    /**
     * Address constructor.
     *
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     * @param DirectoryList $directoryList
     * @param Resolver $localeResolver
     * @param Region $regionModel
     * @param CustomerAddressHelper $addressHelper
     * @param AttributeMetadataDataProvider $attributeMetadataDataProvider
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager,
        DirectoryList $directoryList,
        Resolver $localeResolver,
        Region $regionModel,
        CustomerAddressHelper $addressHelper,
        AttributeMetadataDataProvider $attributeMetadataDataProvider
    ) {
        $this->_directoryList = $directoryList;
        $this->_localeResolver = $localeResolver;
        $this->_regionModel = $regionModel;
        $this->addressHelper = $addressHelper;
        $this->attributeMetadataDataProvider = $attributeMetadataDataProvider;

        parent::__construct($context, $objectManager, $storeManager);
    }

    /**
     * Address Fields
     *
     * @return array
     */
    public function getAddressFields()
    {
        $fieldPosition = $this->getAddressFieldPosition();

        $fields = array_keys($fieldPosition);
        if (!in_array('country_id', $fields)) {
            array_unshift($fields, 'country_id');
        }

        if (in_array('region_id', $fields)) {
            $fields[] = 'region_id_input';
        }

        return $fields;
    }

    /**
     * Get position to display on one step checkout
     *
     * @return array
     */
    public function getAddressFieldPosition()
    {
        $fieldPosition = [];
        $sortedField = $this->getSortedField();
        foreach ($sortedField as $field) {
            $fieldPosition[$field->getAttributeCode()] = [
                'sortOrder' => $field->getSortOrder(),
                'colspan'   => $field->getColspan(),
                'isNewRow'  => $field->getIsNewRow()
            ];
        }

        return $fieldPosition;
    }

    /**
     * Get attribute collection to show on osc and manage field
     *
     * @param bool|true $onlySorted
     *
     * @return array
     */
    public function getSortedField($onlySorted = true)
    {
        $availableFields = [];
        $sortedFields = [];
        $sortOrder = 1;

        $collection = $this->attributeMetadataDataProvider->loadAttributesCollection(
            'customer_address',
            'customer_register_address'
        );
        foreach ($collection as $key => $field) {
            if (!$this->isAddressAttributeVisible($field)) {
                continue;
            }
            $availableFields[] = $field;
        }

        $collection = $this->attributeMetadataDataProvider->loadAttributesCollection(
            'customer',
            'customer_account_create'
        );
        foreach ($collection as $key => $field) {
            if (!$this->isCustomerAttributeVisible($field)) {
                continue;
            }
            $availableFields[] = $field;
        }

        if ($this->isEnableCustomerAttributes()) {
            $collection = $this->attributeMetadataDataProvider->loadAttributesCollection(
                'customer_address',
                'checkout_index_index'
            );
            foreach ($collection as $key => $field) {
                if (!$field->getIsVisible()) {
                    continue;
                }
                $availableFields[] = $field;
            }
        }

        $isNewRow = true;
        $fieldConfig = $this->getFieldPosition();
        foreach ($fieldConfig as $field) {
            foreach ($availableFields as $key => $avField) {
                if ($field['code'] == $avField->getAttributeCode()) {
                    $avField->setColspan($field['colspan'])
                        ->setSortOrder($sortOrder++)
                        ->setIsNewRow($isNewRow);
                    $sortedFields[] = $avField;
                    unset($availableFields[$key]);

                    $this->checkNewRow($field['colspan'], $isNewRow);
                    break;
                }
            }
        }

        return $onlySorted ? $sortedFields : [$sortedFields, $availableFields];
    }

    /**
     * Check if address attribute can be visible on frontend
     *
     * @param Attribute $attribute
     *
     * @return bool|null|string
     */
    public function isAddressAttributeVisible($attribute)
    {
        if ($this->isEnableCustomerAttributes() && $attribute->getIsUserDefined()) {
            return false;
        }

        $code = $attribute->getAttributeCode();
        $result = $attribute->getIsVisible();
        switch ($code) {
            case 'vat_id':
                $result = $this->addressHelper->isVatAttributeVisible();
                break;
            case 'region':
                $result = false;
                break;
        }

        return $result;
    }

    /**
     * Check if customer attribute can be visible on frontend
     *
     * @param Attribute $attribute
     *
     * @return bool|null|string
     */
    public function isCustomerAttributeVisible($attribute)
    {
        if ($this->isEnableCustomerAttributes() && $attribute->getIsUserDefined()) {
            return false;
        }

        $code = $attribute->getAttributeCode();
        if (in_array($code, ['gender', 'taxvat', 'dob'])) {
            return $attribute->getIsVisible();
        } elseif (!$attribute->getIsUserDefined()) {
            return false;
        }

        return true;
    }

    /**
     * @return mixed
     */
    public function getFieldPosition()
    {
        $fields = $this->getConfigValue(self::SORTED_FIELD_POSITION);

        return self::jsonDecode($fields);
    }

    /**
     * @param $colSpan
     * @param $isNewRow
     *
     * @return $this
     */
    private function checkNewRow($colSpan, &$isNewRow)
    {
        if ($colSpan == 6 && $isNewRow) {
            $isNewRow = false;
        } elseif ($colSpan == 12 || ($colSpan == 6 && !$isNewRow)) {
            $isNewRow = true;
        }

        return $this;
    }

    /***************************************** Maxmind Db GeoIp ******************************************************/
    /**
     * @param $storeId
     *
     * @return bool
     */
    public function isEnableGeoIP($storeId = null)
    {
        if (!$this->getConfigGeneral('geoip') || !$this->isModuleOutputEnabled('Mageplaza_GeoIP')) {
            return false;
        }

        $helper = $this->getGeoIPHelper();
        try {
            $hasLib = $helper->checkHasLibrary();
        } catch (Exception $e) {
            $hasLib = false;
        }

        return $helper->isEnabled($storeId) && $hasLib;
    }

    /**
     * @return \Mageplaza\GeoIP\Helper\Address
     */
    protected function getGeoIPHelper()
    {
        return $this->getObject(\Mageplaza\GeoIP\Helper\Address::class);
    }

    /**
     * @return array
     */
    public function getGeoIpData()
    {
        if ($this->isEnableGeoIP()) {
            $geoIpData = $this->getGeoIPHelper()->getGeoIpData();

            $allowedCountries = $this->getConfigValue('general/country/allow');
            $allowedCountries = explode(',', $allowedCountries);
            if (isset($geoIpData['country_id']) && !in_array($geoIpData['country_id'], $allowedCountries)) {
                $geoIpData = [];
            }

            return $geoIpData;
        }

        return [];
    }
}
