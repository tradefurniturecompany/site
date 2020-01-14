<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Transaction;

abstract class AbstractTransaction extends \Hotlink\Brightpearl\Model\Api\Transaction\AbstractTransaction
{
    protected $accountCode;

    public function setAccountCode($accountCode)
    {
        $this->accountCode = $accountCode;
        return $this;
    }

    public function getAccountCode()
    {
        return $this->accountCode;
    }
}
