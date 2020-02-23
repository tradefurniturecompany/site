<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Workflow\Order\Status\Transaction;

class Get extends \Hotlink\Brightpearl\Model\Api\Service\Transaction\AbstractTransaction
{
    protected $externalId;

    function getName()
    {
        return 'Order status request GET';
    }

    protected function _getRequestModel()
    {
        return '\Hotlink\Brightpearl\Model\Api\Service\Workflow\Order\Status\Message\Get\Request';
    }

    protected function _getResponseModel()
    {
        return '\Hotlink\Brightpearl\Model\Api\Service\Workflow\Order\Status\Message\Get\Response';
    }

    function setExternalId( $externalId )
    {
        $this->externalId = $externalId;
        return $this;
    }

    function getExternalId()
    {
        return $this->externalId;
    }
}
