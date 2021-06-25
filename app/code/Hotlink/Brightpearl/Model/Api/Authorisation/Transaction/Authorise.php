<?php
namespace Hotlink\Brightpearl\Model\Api\Authorisation\Transaction;

class Authorise extends \Hotlink\Brightpearl\Model\Api\Transaction\AbstractTransaction
{
    protected $_accountCode;
    protected $_requestToken;

    protected function _getRequestModel()
    {
        return '\Hotlink\Brightpearl\Model\Api\Authorisation\Message\Authorise\Request';
    }

    protected function _getResponseModel()
    {
        return '\Hotlink\Brightpearl\Model\Api\Authorisation\Message\Authorise\Response';
    }

    public function getAccountCode()
    {
        return $this->_accountCode;
    }

    public function setAccountCode( $value )
    {
        $this->_accountCode = $value;
        return $this;
    }

    public function getRequestToken()
    {
        return $this->_requestToken;
    }

    public function setRequestToken( $value )
    {
        $this->_requestToken = $value;
        return $this;
    }
}