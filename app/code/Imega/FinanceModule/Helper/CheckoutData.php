<?php

namespace Imega\FinanceModule\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;

class CheckoutData extends AbstractHelper {

    public function getPositionSelector($scope = 'store'){
        return $this->scopeConfig->getValue(
            'retailfinance/checkout_settings/position_selector',
            $scope
        );
    }


    public function getPaymentMethod($scope = 'store'){
      return $this->scopeConfig->getValue(
          'retailfinance/checkout_settings/payment_method',
          $scope
      );
    }

    public function getLogoCss($scope = 'store'){
      return $this->scopeConfig->getValue(
          'retailfinance/checkout_settings/logo_css',
          $scope
      );
    }

    public function disablePaymentMethodIfNoFilter($scope = 'store'){
      return $this->scopeConfig->getValue(
          'retailfinance/checkout_settings/disable_if_no_filter',
          $scope
      );
    }

    public function checkoutOnPayment($scope = 'store'){
      return $this->scopeConfig->getValue(
          'retailfinance/checkout_settings/checkout_on_payment',
          $scope
      );
    }

  }
