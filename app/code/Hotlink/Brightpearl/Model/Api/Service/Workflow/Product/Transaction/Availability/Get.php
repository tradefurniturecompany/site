<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Workflow\Product\Transaction\Availability;

class Get extends \Hotlink\Brightpearl\Model\Api\Service\Transaction\AbstractTransaction
{
    protected $_skus;
    protected $_warehouses;

    public function getName()
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

    public function setSkus(array $skus)
    {
        $this->_skus = $skus;
        return $this;
    }

    public function setWarehouses(array $warehouses)
    {
        $this->_warehouses = $warehouses;
        return $this;
    }

    public function getSkus()
    {
        return $this->_skus;
    }

    public function getWarehouses()
    {
        return $this->_warehouses;
    }
}