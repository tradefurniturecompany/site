<?php

namespace Imega\FinanceModule\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;

class CartData extends AbstractHelper {

    /*
     * @return bool
     */
    public function widgetIsEnabled($scope = 'store'){
        return $this->scopeConfig->isSetFlag(
            'retailfinance/cartpage/enable',
            $scope
        );
    }


    public function getPositionSelector($scope = 'store'){
        return $this->scopeConfig->getValue(
            'retailfinance/cartpage/position_selector',
            $scope
        );
    }

    public function getPosition($scope = 'store'){
        return $this->scopeConfig->getValue(
            'retailfinance/cartpage/position',
            $scope
        );
    }

    public function getPriceElement($scope = 'store'){
      return $this->scopeConfig->getValue(
          'retailfinance/cartpage/price_selector',
          $scope
      );
    }

    public function getInnerPriceElement($scope = 'store'){
      return $this->scopeConfig->getValue(
          'retailfinance/cartpage/inner_price_selector',
          $scope
      );
    }
    public function getMargin($scope = 'store'){
      return $this->scopeConfig->getValue(
          'retailfinance/cartpage/anchor_margin',
          $scope
      );
    }
    public function getWidth($scope = 'store'){
      return $this->scopeConfig->getValue(
          'retailfinance/cartpage/anchor_width',
          $scope
      );
    }
    public function hideIfNotInRange($scope = 'store'){
      return $this->scopeConfig->getValue(
          'retailfinance/cartpage/hide_if_not_in_range',
          $scope
      );
    }

  }
