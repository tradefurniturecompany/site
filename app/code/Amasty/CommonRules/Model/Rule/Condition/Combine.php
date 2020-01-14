<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CommonRules
 */


namespace Amasty\CommonRules\Model\Rule\Condition;

use Amasty\CommonRules\Model\RegistryConstants;
use Amasty\CommonRules\Model\Rule\Condition\ConditionBuilder as Conditions;
use Amasty\CommonRules\Model\Rule\Factory\HandleFactoryInterface;

class Combine extends \Magento\SalesRule\Model\Rule\Condition\Combine
{
    /**
     * @var string
     */
    protected $conditionsAddressPath = Conditions::AMASTY_COMMON_RULES_PATH_TO_CONDITIONS .'Address';

    /**
     * @var \Amasty\CommonRules\Model\Rule\Factory\HandleFactory
     */
    private $handleFactory;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    private $moduleManager;

    /**
     * @var \Amasty\CommonRules\Model\Rule\Factory\CombineHandleFactory
     */
    private $combineHandleFactory;

    /**
     * Combine constructor.
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param Address $conditionAddress
     * @param array $data
     */
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Amasty\CommonRules\Model\Rule\Condition\Address $conditionAddress,
        \Amasty\CommonRules\Model\Rule\Factory\HandleFactory $handleFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        \Amasty\CommonRules\Model\Rule\Factory\CombineHandleFactory $combineHandleFactory,
        array $data = []
    ) {
        $this->_conditionAddress = $conditionAddress;
        $this->handleFactory = $handleFactory;
        $this->moduleManager = $moduleManager;
        $this->combineHandleFactory = $combineHandleFactory;
        parent::__construct($context, $eventManager, $conditionAddress, $data);
        $this->setType(Conditions::AMASTY_COMMON_RULES_PATH_TO_CONDITIONS . 'Combine');
    }

    /**
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $conditions = [
            [
                'value' => '',
                'label' => __('Please Choose a Condition to Add.')
            ]
        ];
        $conditions = array_merge_recursive(
            $conditions,
            [
                [
                    'value' => Conditions::AMASTY_COMMON_RULES_PATH_TO_CONDITIONS . 'Product\Subselect',
                    'label' => __('Products Subselection')
                ],
                [
                    'value' => $this->getType(),
                    'label' => __('Conditions Combination')
                ],
                [
                    'label' => __('Cart Attribute'),
                    'value' => $this->getAddressAttributes()
                ]
            ]
        );

        $additional = new \Magento\Framework\DataObject();
        $this->_eventManager->dispatch(
            'salesrule_rule_condition_combine',
            [
                'additional' => $additional
            ]
        );

        $additionalConditions = $additional->getConditions();

        if ($additionalConditions) {
            $conditions = array_merge_recursive($conditions, $additionalConditions);
        }

        return $conditions;
    }

    /**
     * @return array
     */
    protected function getAddressAttributes()
    {
        $addressAttributes = $this->_conditionAddress->loadAttributeOptions()->getAttributeOption();
        $attributes = [];

        foreach ($addressAttributes as $code => $label) {
            $attributes[] = [
                'value' => $this->conditionsAddressPath . '|' . $code,
                'label' => $label,
            ];
        }

        return $attributes;
    }

    /**
     * @return bool
     */
    public function validateNotModel($entity)
    {
        if (!$this->getConditions()) {
            return true;
        }

        $all = $this->getAggregator() === 'all';
        $true = (bool)$this->getValue();

        foreach ($this->getConditions() as $cond) {
            if ($entity instanceof \Magento\Framework\Model\AbstractModel) {
                $validated = $cond->validate($entity);
            } elseif ($entity instanceof \Magento\Framework\DataObject
                && method_exists($cond, 'validateNotModel')
            ) {
                $validated = $cond->validateNotModel($entity);
            } elseif ($entity instanceof \Magento\Framework\DataObject) {
                $attribute = $entity->getData($cond->getAttribute());
                $validated = $cond->validateAttribute($attribute);
            } else {
                $validated = $cond->validateByEntityId($entity);
            }
            if ($all && $validated !== $true) {
                return false;
            } elseif (!$all && $validated === $true) {
                return true;
            }
        }
        return $all ? true : false;
    }
}
