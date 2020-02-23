<?php
namespace Hotlink\Brightpearl\Model\Api\Utility\Transaction\Account;

class Locate extends \Hotlink\Brightpearl\Model\Api\Transaction\AbstractTransaction
{

    protected $accountCode;

    function getName()
    {
        return 'Account Location';
    }

    protected function _getRequestModel()
    {
        return '\Hotlink\Brightpearl\Model\Api\Utility\Message\Account\Locate\Request';
    }

    protected function _getResponseModel()
    {
        return '\Hotlink\Brightpearl\Model\Api\Utility\Message\Account\Locate\Response';
    }

    function getAccountCode()
    {
        return $this->accountCode;
    }

    function setAccountCode($accountCode)
    {
        $this->accountCode = $accountCode;
        return $this;
    }

    function getDevRef()
    {
        return \Hotlink\Brightpearl\Model\Platform::DEV_REF;
    }

}