<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Workflow\Product\Transaction\Skus;

class Post extends \Hotlink\Brightpearl\Model\Api\Service\Transaction\AbstractTransaction
{
    protected $_skus;

    public function getName()
    {
        return 'Product Instance POST';
    }

    protected function _getRequestModel()
    {
        return '\Hotlink\Brightpearl\Model\Api\Service\Workflow\Product\Message\Skus\Post\Request';
    }

    protected function _getResponseModel()
    {
        return '\Hotlink\Brightpearl\Model\Api\Service\Workflow\Product\Message\Skus\Post\Response';
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
}