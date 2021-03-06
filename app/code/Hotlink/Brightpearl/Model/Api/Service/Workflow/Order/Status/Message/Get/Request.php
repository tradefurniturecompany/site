<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Workflow\Order\Status\Message\Get;

class Request extends \Hotlink\Brightpearl\Model\Api\Service\Message\Request\Get\AbstractGet
{

    function getFunction()
    {
        return $this->getMethod(). " workflow-integration-service/order";
    }

    function getAction()
    {
        return sprintf(
            '/2.0.0/%s/workflow-integration-service/order/%s',
            $this->getTransaction()->getAccountCode(),
            self::encodeParam( $this->getTransaction()->getExternalId() ) );
    }

    function validate()
    {
        parent::validate();
        return $this->_assertNotEmpty( $this->getTransaction()->getExternalId(), 'externalId' );
    }
}
