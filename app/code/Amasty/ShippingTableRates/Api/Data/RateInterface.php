<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingTableRates
 */


namespace Amasty\ShippingTableRates\Api\Data;

interface RateInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    const ID = 'id';
    const METHOD_ID = 'method_id';
    const COUNTRY = 'country';
    const STATE = 'state';
    const ZIP_FROM = 'zip_from';
    const ZIP_TO = 'zip_to';
    const PRICE_FROM = 'price_from';
    const PRICE_TO = 'price_to';
    const WEIGHT_FROM = 'weight_from';
    const WEIGHT_TO = 'weight_to';
    const QTY_FROM = 'qty_from';
    const QTY_TO = 'qty_to';
    const SHIPPING_TYPE = 'shipping_type';
    const COST_BASE = 'cost_base';
    const COST_PERCENT = 'cost_percent';
    const COST_PRODUCT = 'cost_product';
    const COST_WEIGHT = 'cost_weight';
    const TIME_DELIVERY = 'time_delivery';
    const NUM_ZIP_FROM = 'num_zip_from';
    const NUM_ZIP_TO = 'num_zip_to';
    const CITY = 'city';
    const NAME_DELIVERY = 'name_delivery';
    /**#@-*/

    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $id
     *
     * @return \Amasty\ShippingTableRates\Api\Data\RateInterface
     */
    public function setId($id);

    /**
     * @return int
     */
    public function getMethodId();

    /**
     * @param int $methodId
     *
     * @return \Amasty\ShippingTableRates\Api\Data\RateInterface
     */
    public function setMethodId($methodId);

    /**
     * @return string
     */
    public function getCountry();

    /**
     * @param string $country
     *
     * @return \Amasty\ShippingTableRates\Api\Data\RateInterface
     */
    public function setCountry($country);

    /**
     * @return int
     */
    public function getState();

    /**
     * @param int $state
     *
     * @return \Amasty\ShippingTableRates\Api\Data\RateInterface
     */
    public function setState($state);

    /**
     * @return string
     */
    public function getZipFrom();

    /**
     * @param string $zipFrom
     *
     * @return \Amasty\ShippingTableRates\Api\Data\RateInterface
     */
    public function setZipFrom($zipFrom);

    /**
     * @return string
     */
    public function getZipTo();

    /**
     * @param string $zipTo
     *
     * @return \Amasty\ShippingTableRates\Api\Data\RateInterface
     */
    public function setZipTo($zipTo);

    /**
     * @return float
     */
    public function getPriceFrom();

    /**
     * @param float $priceFrom
     *
     * @return \Amasty\ShippingTableRates\Api\Data\RateInterface
     */
    public function setPriceFrom($priceFrom);

    /**
     * @return float
     */
    public function getPriceTo();

    /**
     * @param float $priceTo
     *
     * @return \Amasty\ShippingTableRates\Api\Data\RateInterface
     */
    public function setPriceTo($priceTo);

    /**
     * @return float
     */
    public function getWeightFrom();

    /**
     * @param float $weightFrom
     *
     * @return \Amasty\ShippingTableRates\Api\Data\RateInterface
     */
    public function setWeightFrom($weightFrom);

    /**
     * @return float
     */
    public function getWeightTo();

    /**
     * @param float $weightTo
     *
     * @return \Amasty\ShippingTableRates\Api\Data\RateInterface
     */
    public function setWeightTo($weightTo);

    /**
     * @return float
     */
    public function getQtyFrom();

    /**
     * @param float $qtyFrom
     *
     * @return \Amasty\ShippingTableRates\Api\Data\RateInterface
     */
    public function setQtyFrom($qtyFrom);

    /**
     * @return float
     */
    public function getQtyTo();

    /**
     * @param float $qtyTo
     *
     * @return \Amasty\ShippingTableRates\Api\Data\RateInterface
     */
    public function setQtyTo($qtyTo);

    /**
     * @return int
     */
    public function getShippingType();

    /**
     * @param int $shippingType
     *
     * @return \Amasty\ShippingTableRates\Api\Data\RateInterface
     */
    public function setShippingType($shippingType);

    /**
     * @return float
     */
    public function getCostBase();

    /**
     * @param float $costBase
     *
     * @return \Amasty\ShippingTableRates\Api\Data\RateInterface
     */
    public function setCostBase($costBase);

    /**
     * @return float
     */
    public function getCostPercent();

    /**
     * @param float $costPercent
     *
     * @return \Amasty\ShippingTableRates\Api\Data\RateInterface
     */
    public function setCostPercent($costPercent);

    /**
     * @return float
     */
    public function getCostProduct();

    /**
     * @param float $costProduct
     *
     * @return \Amasty\ShippingTableRates\Api\Data\RateInterface
     */
    public function setCostProduct($costProduct);

    /**
     * @return float
     */
    public function getCostWeight();

    /**
     * @param float $costWeight
     *
     * @return \Amasty\ShippingTableRates\Api\Data\RateInterface
     */
    public function setCostWeight($costWeight);

    /**
     * @return string|null
     */
    public function getTimeDelivery();

    /**
     * @param string|null $timeDelivery
     *
     * @return \Amasty\ShippingTableRates\Api\Data\RateInterface
     */
    public function setTimeDelivery($timeDelivery);

    /**
     * @return int|null
     */
    public function getNumZipFrom();

    /**
     * @param int|null $numZipFrom
     *
     * @return \Amasty\ShippingTableRates\Api\Data\RateInterface
     */
    public function setNumZipFrom($numZipFrom);

    /**
     * @return int|null
     */
    public function getNumZipTo();

    /**
     * @param int|null $numZipTo
     *
     * @return \Amasty\ShippingTableRates\Api\Data\RateInterface
     */
    public function setNumZipTo($numZipTo);

    /**
     * @return string
     */
    public function getCity();

    /**
     * @param string $city
     *
     * @return \Amasty\ShippingTableRates\Api\Data\RateInterface
     */
    public function setCity($city);

    /**
     * @return string|null
     */
    public function getNameDelivery();

    /**
     * @param string|null $nameDelivery
     *
     * @return \Amasty\ShippingTableRates\Api\Data\RateInterface
     */
    public function setNameDelivery($nameDelivery);
}
