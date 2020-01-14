<?php

namespace Imega\FinanceModule\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;

class Data extends AbstractHelper {

    public function getApiKey($scope = 'store'){
        return $this->scopeConfig->getValue(
            'retailfinance/module_settings/api_key',
            $scope
        );
    }

    public function getEncryptionKey($scope = 'store'){
        return $this->scopeConfig->getValue(
            'retailfinance/module_settings/enc_key',
            $scope
        );
    }

    public function getFinanceProvider($scope = 'store'){
        return $this->scopeConfig->getValue(
            'retailfinance/module_settings/finance_provider',
            $scope
        );
    }


  }
