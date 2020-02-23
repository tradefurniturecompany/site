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

    function getAccountCode()
    {
        return $this->_accountCode;
    }

    function setAccountCode( $value )
    {
        $this->_accountCode = $value;
        return $this;
    }

    function getRequestToken()
    {
        return $this->_requestToken;
    }

    function setRequestToken( $value )
    {
        $this->_requestToken = $value;
        return $this;
    }
}