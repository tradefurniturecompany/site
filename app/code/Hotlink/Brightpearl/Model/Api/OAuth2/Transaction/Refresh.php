<?php
namespace Hotlink\Brightpearl\Model\Api\OAuth2\Transaction;

class Refresh extends \Hotlink\Brightpearl\Model\Api\Transaction\AbstractTransaction
{

    protected $_account;
    protected $_refreshToken;
    protected $_clientId;

    protected function _getRequestModel()
    {
        return \Hotlink\Brightpearl\Model\Api\OAuth2\Message\Refresh\Request::class;
    }

    protected function _getResponseModel()
    {
        return \Hotlink\Brightpearl\Model\Api\OAuth2\Message\Refresh\Response::class;
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

    function getRefreshToken()
    {
        return $this->_refreshToken;
    }

    function setRefreshToken( $value )
    {
        $this->_refreshToken = $value;
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