<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Workflow\Product\Message\Skus\Post;

class Request extends \Hotlink\Brightpearl\Model\Api\Service\Message\Request\Post\AbstractPost
{

    public function getFunction()
    {
        return $this->getMethod(). " workflow-integration-service/instance-products";
    }

    public function getAction()
    {
        // TODO: replace hardcoded api version
        return sprintf('/2.0.0/%s/workflow-integration-service/instance-products', $this->getTransaction()->getAccountCode());
    }

    public function validate()
    {
        return $this->_assertNotEmpty($this->getTransaction()->getSkus(), 'skus');
    }

    public function getBody()
    {
        return $this->jsonHelper()->jsonEncode( $this->getTransaction()->getSkus() );
    }

}