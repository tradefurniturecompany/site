<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Workflow\Product\Transaction\Availability;

class Get extends \Hotlink\Brightpearl\Model\Api\Service\Transaction\AbstractTransaction
{
    protected $_skus;
    protected $_warehouses;

    function getName()
    {
        return 'Product Availability GET';
    }

    protected function _getRequestModel()
    {
        return '\Hotlink\Brightpearl\Model\Api\Service\Workflow\Product\Message\Availability\Get\Request';
    }

    protected function _getResponseModel()
    {
        return '\Hotlink\Brightpearl\Model\Api\Service\Workflow\Product\Message\Availability\Get\Response';
    }

    function setSkus(array $skus)
    {
        $this->_skus = $skus;
        return $this;
    }

    function setWarehouses(array $warehouses)
    {
        $this->_warehouses = $warehouses;
        return $this;
    }

    function getSkus()
    {
        return $this->_skus;
    }

    function getWarehouses()
    {
        return $this->_warehouses;
    }
}