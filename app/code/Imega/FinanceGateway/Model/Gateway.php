<?php

namespace Imega\FinanceGateway\Model;

class Gateway extends \Magento\Payment\Model\Method\AbstractMethod
{

    /**
     * Payment code
     *
     * @var string
     */
     protected $_code = 'financegateway';

     /**
      * Availability option
      *
      * @var bool
      */
     protected $_isOffline = true;

     /**
      * Initialization option
      *
      * @var bool
      */
     protected $_isInitializeNeeded = true;

     protected $_canCapture = true;
     protected $_isGateway = true;


}
