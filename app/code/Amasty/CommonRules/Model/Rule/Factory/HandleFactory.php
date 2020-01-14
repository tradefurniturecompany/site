<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CommonRules
 */


namespace Amasty\CommonRules\Model\Rule\Factory;

use Amasty\CommonRules\Model\Rule\Condition\ConditionBuilder as Conditions;

class HandleFactory extends HandlerFactoryAbstract
{
    /**
     * HandleFactory constructor.
     * @param array $handlers
     */
    function __construct(
        array $handlers
    ) {
        $this->handlers = $handlers;
    }

    /**
     * @param string $type
     * @return array
     */
    protected function getConditionsByType($type)
    {
        $typeCode = ucfirst($type);
        $conditions = [];

        if ($condition = $this->getHandlerByType($type)) {
            $conditionAttributes = $condition->loadAttributeOptions()->getAttributeOption();

            $attributes = [];
            foreach ($conditionAttributes as $code => $label) {
                $attributes[] = [
                    'value' => Conditions::AMASTY_COMMON_RULES_PATH_TO_CONDITIONS . $typeCode . '|' . $code,
                    'label' => $label,
                ];
            }

            $conditions[] = [
                'value' => $attributes,
                'label' => $condition->getConditionLabel(),
            ];
        }

        return $conditions;
    }
}

