<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CommonRules
 */


namespace Amasty\CommonRules\Model\Rule\Condition\Product;

use Amasty\CommonRules\Model\Rule\Condition\ConditionBuilder as Conditions;

class Subselect extends \Magento\SalesRule\Model\Rule\Condition\Product\Subselect
{
    /**
     * @var Conditions
     */
    private $conditionBuilder;

    /**
     * Subselect constructor.
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param \Amasty\CommonRules\Model\Rule\Condition\Product $ruleConditionProduct
     * @param array $data
     */
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Amasty\CommonRules\Model\Rule\Condition\Product $ruleConditionProduct,
        \Amasty\CommonRules\Model\Rule\Condition\ConditionBuilder $conditionBuilder,
        array $data = []
    ) {
        $this->conditionBuilder = $conditionBuilder;
        parent::__construct($context, $ruleConditionProduct, $data);
        $this->setType(Conditions::AMASTY_COMMON_RULES_PATH_TO_CONDITIONS . 'Product\Subselect')->setValue(null);
    }

    /**
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $conditions = parent::getNewChildSelectOptions();

        return $this->conditionBuilder->getChangedNewChildSelectOptions($conditions);
    }

    /**
     * @return $this
     */
    public function loadAttributeOptions()
    {
        $this->setAttributeOption(array(
            'qty'                       => __('total quantity'),
            'base_row_total'            => __('total amount excl. tax'),
            'base_row_total_incl_tax'   => __('total amount incl. tax'),
            'row_weight'                => __('total weight'),
        ));

        return $this;
    }

    /**
     * validate
     *
     * @param Varien_Object $object Quote
     * @return boolean
     */
    public function validate(\Magento\Framework\Model\AbstractModel $object)
    {
        return $this->validateNotModel($object);
    }

    /**
     * @param $object
     * @return bool
     */
    public function validateNotModel($object)
    {
        $attr = $this->getAttribute();
        $total = 0;
        $items = $object->getAllItems() ? $object->getAllItems() : $object->getItemsToValidateRestrictions();
        if ($items) {
            $validIds = [];
            foreach ($items as $item) {
                if ($item->getProduct()->getTypeId() == 'configurable') {
                    $item->getProduct()->setTypeId('skip');
                }

                //can't use parent here
                if (\Magento\SalesRule\Model\Rule\Condition\Product\Combine::validate($item)) {
                    $itemParentId = $item->getParentItemId();

                    if (is_null($itemParentId)) {
                        $validIds[] = $item->getItemId();
                    } else {
                        if (in_array($itemParentId, $validIds)) {
                            continue;
                        } else {
                            $validIds[] = $itemParentId;
                        }
                    }

                    $total += $item->getData($attr);
                }

                if ($item->getProduct()->getTypeId() === 'skip') {
                    $item->getProduct()->setTypeId('configurable');
                }
            }
        }

        return $this->validateAttribute($total);
    }
}
