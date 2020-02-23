<?php
namespace Hotlink\Brightpearl\Model\Api\OAuth2\Transaction;

class Authorise extends \Hotlink\Brightpearl\Model\Api\Transaction\AbstractTransaction
{
    protected $_account;
    protected $_code;
    protected $_redirectUri;
    protected $_clientId;

    protected function _getRequestModel()
    {
        return '\Hotlink\Brightpearl\Model\Api\OAuth2\Message\Authorise\Request';
    }

    protected function _getResponseModel()
    {
        return '\Hotlink\Brightpearl\Model\Api\OAuth2\Message\Authorise\Response';
    }

    function getAccount()
    {
        return $this->_account;
    }

    function setAccount( $value )
    {
        $this->_account = $value;
        return $this;
    }

    function getCode()
    {
        return $this->_code;
    }

    function setCode( $value )
    {
        $this->_code = $value;
        return $this;
    }

    function getRedirectUri()
    {
        return $this->_redirectUri;
    }

    function setRedirectUri( $value )
    {
        $this->_redirectUri = $value;
        return $this;
    }

    function getClientId()
    {
        return $this->_clientId;
    }

    function setClientId( $value )
    {
        $this->_clientId = $value;
        return $this;
    }

    function getContentType()
    {
        return 'application/x-www-form-urlencoded';
    }

}