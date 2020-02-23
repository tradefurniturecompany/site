<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Transaction;

abstract class AbstractTransaction extends \Hotlink\Brightpearl\Model\Api\Transaction\AbstractTransaction
{
    protected $accountCode;

    function setAccountCode($accountCode)
    {
        $this->accountCode = $accountCode;
        return $this;
    }

    function getAccountCode()
    {
        return $this->accountCode;
    }
}
