<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Workflow\Product\Message\Skus\Post;

class Request extends \Hotlink\Brightpearl\Model\Api\Service\Message\Request\Post\AbstractPost
{

    function getFunction()
    {
        return $this->getMethod(). " workflow-integration-service/instance-products";
    }

    function getAction()
    {
        // TODO: replace hardcoded api version
        return sprintf('/2.0.0/%s/workflow-integration-service/instance-products', $this->getTransaction()->getAccountCode());
    }

    function validate()
    {
        return $this->_assertNotEmpty($this->getTransaction()->getSkus(), 'skus');
    }

    function getBody()
    {
        return $this->jsonHelper()->jsonEncode( $this->getTransaction()->getSkus() );
    }

}