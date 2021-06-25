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

    public function getAccount()
    {
        return $this->_account;
    }

    public function setAccount( $value )
    {
        $this->_account = $value;
        return $this;
    }

    public function getCode()
    {
        return $this->_code;
    }

    public function setCode( $value )
    {
        $this->_code = $value;
        return $this;
    }

    public function getRedirectUri()
    {
        return $this->_redirectUri;
    }

    public function setRedirectUri( $value )
    {
        $this->_redirectUri = $value;
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