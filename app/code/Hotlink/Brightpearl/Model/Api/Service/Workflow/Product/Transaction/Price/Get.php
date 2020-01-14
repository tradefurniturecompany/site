<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Workflow\Product\Transaction\Price;

class Get extends \Hotlink\Brightpearl\Model\Api\Service\Transaction\AbstractTransaction
{
    protected $_skus;
    protected $_pricelists;

    public function getName()
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

    public function setSkus(array $skus)
    {
        $this->_skus = $skus;
        return $this;
    }

    public function getSkus()
    {
        return $this->_skus;
    }

    public function setPricelists(array $pricelists)
    {
        $this->_pricelists = $pricelists;
        return $this;
    }

    public function getPricelists()
    {
        return $this->_pricelists;
    }
}