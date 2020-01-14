<?php

namespace Interactivated\TF\Block\Frontend;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\AbstractBlock;

class Banner extends Template {

    public function getIsSticky()
    {
        return $this->_scopeConfig->getValue(
            'interactivated_entry_1/general/interactivated_homepagebanner_sticky',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

}