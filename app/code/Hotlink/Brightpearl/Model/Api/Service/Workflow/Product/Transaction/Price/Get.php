<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Workflow\Product\Transaction\Price;

class Get extends \Hotlink\Brightpearl\Model\Api\Service\Transaction\AbstractTransaction
{
    protected $_skus;
    protected $_pricelists;

    function getName()
    {
        return 'Product Pricing GET';
    }

    protected function _getRequestModel()
    {
        return '\Hotlink\Brightpearl\Model\Api\Service\Workflow\Product\Message\Price\Get\Request';
    }

    protected function _getResponseModel()
    {
        return '\Hotlink\Brightpearl\Model\Api\Service\Workflow\Product\Message\Price\Get\Response';
    }

    function setSkus(array $skus)
    {
        $this->_skus = $skus;
        return $this;
    }

    function getSkus()
    {
        return $this->_skus;
    }

    function setPricelists(array $pricelists)
    {
        $this->_pricelists = $pricelists;
        return $this;
    }

    function getPricelists()
    {
        return $this->_pricelists;
    }
}