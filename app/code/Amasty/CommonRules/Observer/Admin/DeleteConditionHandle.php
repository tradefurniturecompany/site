<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CommonRules
 */


namespace Amasty\CommonRules\Observer\Admin;

class DeleteConditionHandle implements \Magento\Framework\Event\ObserverInterface
{
    const NOT_SUPPORTED_CONDITIONS = [
        'Amasty\Conditions\Model\Rule\Condition\Address|payment_method',
    ];

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    private $request;

    public function __construct(\Magento\Framework\App\Request\Http $request)
    {
        $this->request = $request;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return DeleteConditionHandle
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $moduleName = $this->request->getModuleName();
        if ($this->isCommonRulesModule($moduleName)) {
            $conditions = $observer->getAdditional()->getConditions();
            $promoConditions = [];

            if (is_array($conditions)) {
                foreach ($conditions as $condition) {
                    if ($this->isAdvancedConditions($condition)) {
                        foreach ($condition['value'] as $key => $condAttribute) {
                            if (in_array($condAttribute['value'], self::NOT_SUPPORTED_CONDITIONS)) {
                                unset($condition['value'][$key]);
                            }
                        }
                    }
                    $promoConditions[] = $condition;
                }

                $observer->getAdditional()->setConditions($promoConditions);
            }
        }

        return $this;
    }

    /**
     * @param $condition
     *
     * @return bool
     */
    private function isAdvancedConditions($condition)
    {
        return is_array($condition)
            && isset($condition['label'])
            && $condition['label']->getText() === \Amasty\Conditions\Model\Constants::MODULE_NAME;
    }

    /**
     * @param $moduleName
     *
     * @return bool
     */
    private function isCommonRulesModule($moduleName)
    {
        return $moduleName === 'amasty_shiprestriction'
            || $moduleName === 'amasty_shiprules'
            || $moduleName === 'amasty_payrestriction';
    }
}
