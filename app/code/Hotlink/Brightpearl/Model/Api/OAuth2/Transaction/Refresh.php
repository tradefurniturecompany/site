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

    public function getAccount()
    {
        return $this->_account;
    }

    public function setAccount( $value )
    {
        $this->_account = $value;
        return $this;
    }

    public function getRefreshToken()
    {
        return $this->_refreshToken;
    }

    public function setRefreshToken( $value )
    {
        $this->_refreshToken = $value;
        return $this;
    }

    public function getClientId()
    {
        return $this->_clientId;
    }

    public function setClientId( $value )
    {
        $this->_clientId = $value;
        return $this;
    }

    public function getContentType()
    {
        return 'application/x-www-form-urlencoded';
    }
    
}