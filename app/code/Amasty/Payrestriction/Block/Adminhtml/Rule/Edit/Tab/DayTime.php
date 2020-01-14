<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Payrestriction
 */


namespace Amasty\Payrestriction\Block\Adminhtml\Rule\Edit\Tab;

use Amasty\CommonRules\Block\Adminhtml\Rule\Edit\Tab\Daystime as CommonRulesDaytime;
use Amasty\Payrestriction\Model\RegistryConstants;

class DayTime extends CommonRulesDaytime
{
    public function _construct()
    {
        $this->setRegistryKey(RegistryConstants::REGISTRY_KEY);
        parent::_construct();
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Days & Time');
    }
}
