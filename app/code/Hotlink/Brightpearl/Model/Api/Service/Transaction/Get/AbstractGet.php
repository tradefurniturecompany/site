<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Transaction\Get;

abstract class AbstractGet extends \Hotlink\Brightpearl\Model\Api\Service\Transaction\AbstractTransaction
{

    /**
     * @see https://www.brightpearl.com/developer/latest/concept/id-set.html
     */
    protected $idSet;

    function setIdSet( $idSet )
    {
        $this->idSet = $idSet;
        return $this;
    }

    function geIdSet()
    {
        return $this->idSet;
    }
}