<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Order\Transaction\Field\Metadata;

class Get extends \Hotlink\Brightpearl\Model\Api\Service\Transaction\Get\AbstractGet
{
    protected $source;

    public function setSource( $source )
    {
        $this->source = $source;
        return $this;
    }

    public function getSource()
    {
        return $this->source;
    }

    public function getName()
    {
        return 'Order Custom Field Metadata GET';
    }

    protected function _getRequestModel()
    {
        return '\Hotlink\Brightpearl\Model\Api\Service\Order\Message\Field\Metadata\Get\Request';
    }

    protected function _getResponseModel()
    {
        return '\Hotlink\Brightpearl\Model\Api\Service\Order\Message\Field\Metadata\Get\Response';
    }
}