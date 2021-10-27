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

    public function getEnableOrderEmailOnCustomStatus($scope = 'store'){
        return $this->scopeConfig->getValue(
            'retailfinance/module_settings/enable_order_email_status',
            $scope
        );
    }

    public function getOrderEmailStatus($scope = 'store'){
        return $this->scopeConfig->getValue(
            'retailfinance/module_settings/order_email_status',
            $scope
        );
    }

    public function checkoutOnPayment($scope = 'store'){
      return $this->scopeConfig->getValue(
          'retailfinance/checkout_settings/checkout_on_payment',
          $scope
      );
    }

    public function getPriceSelector($scope = 'store'){
      return $this->scopeConfig->getValue(
          'retailfinance/checkout_settings/price_selector',
          $scope
      );
    }

    public function getInnerPriceSelector($scope = 'store'){
      return $this->scopeConfig->getValue(
          'retailfinance/checkout_settings/inner_price_selector',
          $scope
      );
    }

    public function getPositionSelector($scope = 'store'){
      return $this->scopeConfig->getValue(
          'retailfinance/checkout_settings/payment_position_selector',
          $scope
      );
    }

    public function getPosition($scope = 'store'){
      return $this->scopeConfig->getValue(
          'retailfinance/checkout_settings/position',
          $scope
      );
    }


  }
