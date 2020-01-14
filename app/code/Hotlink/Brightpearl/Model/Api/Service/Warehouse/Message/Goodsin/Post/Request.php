<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Warehouse\Message\Goodsin\Post;

class Request extends \Hotlink\Brightpearl\Model\Api\Service\Message\Request\Post\AbstractPost
{

    public function getFunction()
    {
        return $this->getMethod(). " warehouse-service/order/{id}/goods-note/goods-in";
    }

    public function getAction()
    {
        return sprintf( '/public-api/%s/warehouse-service/order/%s/goods-note/goods-in/',
                        $this->getTransaction()->getAccountCode(),
                        $this->getTransaction()->getPurchaseOrderId() );
    }

    public function validate()
    {
        return $this->_assertNotEmpty( $this->getTransaction()->getNote(), 'note' );
    }

    public function getBody()
    {
        return $this->jsonHelper()->jsonEncode( $this->getTransaction()->getNote() );
    }

}