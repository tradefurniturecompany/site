<?php

namespace Imega\FinanceModule\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;

class ProductData extends AbstractHelper {

    /*
     * @return bool
     */
    public function widgetIsEnabled($scope = 'store'){
        return $this->scopeConfig->isSetFlag(
            'retailfinance/productpage/enable',
            $scope
        );
    }


    public function getPositionSelector($scope = 'store'){
        return $this->scopeConfig->getValue(
            'retailfinance/productpage/position_selector',
            $scope
        );
    }

    public function getPosition($scope = 'store'){
        return $this->scopeConfig->getValue(
            'retailfinance/productpage/position',
            $scope
        );
    }

    public function getPriceElement($scope = 'store'){
      return $this->scopeConfig->getValue(
          'retailfinance/productpage/price_selector',
          $scope
      );
    }

    public function getInnerPriceElement($scope = 'store'){
      return $this->scopeConfig->getValue(
          'retailfinance/productpage/inner_price_selector',
          $scope
      );
    }
    public function getMargin($scope = 'store'){
      return $this->scopeConfig->getValue(
          'retailfinance/productpage/anchor_margin',
          $scope
      );
    }
    public function getWidth($scope = 'store'){
      return $this->scopeConfig->getValue(
          'retailfinance/productpage/anchor_width',
          $scope
      );
    }
    public function showInline($scope = 'store'){
      return $this->scopeConfig->getValue(
          'retailfinance/productpage/show_inline',
          $scope
      );
    }
    public function getInlineSelector($scope = 'store'){
      return $this->scopeConfig->getValue(
          'retailfinance/productpage/inline_selector',
          $scope
      );
    }
    public function hideIfNotInRange($scope = 'store'){
      return $this->scopeConfig->getValue(
          'retailfinance/productpage/hide_if_not_in_range',
          $scope
      );
    }



  }
