<?php

namespace Imega\FinanceModule\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;

class CategoryData extends AbstractHelper {

    /*
     * @return bool
     */
    public function widgetIsEnabled($scope = 'store'){
        return $this->scopeConfig->isSetFlag(
            'retailfinance/categorypage/enable',
            $scope
        );
    }

    public function getElement($scope = 'store'){
        return $this->scopeConfig->getValue(
            'retailfinance/categorypage/outer_selector',
            $scope
        );
    }


    public function getPositionSelector($scope = 'store'){
        return $this->scopeConfig->getValue(
            'retailfinance/categorypage/position_selector',
            $scope
        );
    }

    public function getPosition($scope = 'store'){
        return $this->scopeConfig->getValue(
            'retailfinance/categorypage/position',
            $scope
        );
    }

    public function getPriceElement($scope = 'store'){
      return $this->scopeConfig->getValue(
          'retailfinance/categorypage/price_selector',
          $scope
      );
    }

    public function getPrefix($scope = 'store'){
      return $this->scopeConfig->getValue(
          'retailfinance/categorypage/prefix',
          $scope
      );
    }

    public function getSuffix($scope = 'store'){
      return $this->scopeConfig->getValue(
          'retailfinance/categorypage/suffix',
          $scope
      );
    }

    public function getCss($scope = 'store'){
      return $this->scopeConfig->getValue(
          'retailfinance/categorypage/custom_css',
          $scope
      );
    }

  }
