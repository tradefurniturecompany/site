<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CommonRules
 */


namespace Amasty\CommonRules\Model;

class Rule extends \Magento\Rule\Model\AbstractModel
{
    const ALL_ORDERS = 0;
    const BACKORDERS_ONLY = 1;
    const NON_BACKORDERS = 2;

    const SALES_RULE_PRODUCT_CONDITION_NAMESPACE = 'Magento\\SalesRule\\Model\\Rule\\Condition\\Product';

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Rule\Condition\Combine
     */
    protected $conditionCombine;

    /**
     * @var Rule\Condition\Product\Combine
     */
    protected $conditionProductCombine;

    /**
     * @var \Amasty\Base\Model\Serializer
     */
    protected $serializer;

    /**
     * @var \Amasty\CommonRules\Model\Modifiers\Subtotal
     */
    protected $subtotalModifier;

    /**
     * @var Validator\Backorder
     */
    protected $backorderValidator;

    /**
     * Rule constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param Rule\Condition\Combine $conditionCombine
     * @param Rule\Condition\Product\Combine $conditionProductCombine
     * @param \Amasty\Base\Model\Serializer $serializer
     * @param ResourceModel\AbstractRule|null $resource
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Amasty\CommonRules\Model\Rule\Condition\CombineFactory $conditionCombine,
        \Amasty\CommonRules\Model\Rule\Condition\Product\CombineFactory $conditionProductCombine,
        \Amasty\Base\Model\Serializer $serializer,
        \Amasty\CommonRules\Model\Modifiers\Subtotal $subtotalModifier,
        \Amasty\CommonRules\Model\Validator\Backorder $backorderValidator,
        $resource = null,
        array $data = []
    )
    {
        $this->conditionCombine = $conditionCombine->create();
        $this->conditionProductCombine = $conditionProductCombine->create();
        $this->storeManager = $storeManager;
        $this->serializer = $serializer;
        $this->subtotalModifier = $subtotalModifier;
        $this->backorderValidator = $backorderValidator;
        parent::__construct(
            $context, $registry, $formFactory, $localeDate, $resource, null, $data
        );
    }

    public function getConditionsInstance()
    {
        return $this->conditionCombine;
    }

    public function getActionsInstance()
    {
        return $this->conditionProductCombine;
    }

    /**
     * @return $this
     */
    public function afterSave()
    {
        //Saving attributes used in rule
        $ruleProductAttributes = array_merge(
            $this->_getUsedAttributes($this->getConditionsSerialized()),
            $this->_getUsedAttributes($this->getActionsSerialized())
        );

        if (count($ruleProductAttributes)) {
            $this->_resource->saveAttributes($this->getId(), $ruleProductAttributes);
        }

        return parent::afterSave();
    }

    /**
     * Return all product attributes used on serialized action or condition
     *
     */
    protected function _getUsedAttributes($serializedString)
    {
        $result = [];
        $serializedString = $this->serializer->unserialize($serializedString);

        if (is_array($serializedString) && array_key_exists('conditions', $serializedString)) {
            $result = $this->recursiveFindAttributes($serializedString);
        }

        return $result;
    }

    /**
     * @param $loop
     * @return array
     */
    protected function recursiveFindAttributes($loop)
    {
        $arrayIterator = new \RecursiveIteratorIterator(
            new \RecursiveArrayIterator($loop)
        );

        $result = [];
        $nextAttribute = false;
        foreach ($arrayIterator as $key => $value) {
            if ($key == 'type' && $value == self::SALES_RULE_PRODUCT_CONDITION_NAMESPACE) {
                $nextAttribute = true;
            }

            if ($key == 'attribute' && $nextAttribute) {
                $result[] = $value;
                $nextAttribute = false;
            }
        }

        return $result;
    }

    /**
     * @return $this
     */
    protected function _setWebsiteIds()
    {
        $websites = [];

        foreach ($this->storeManager->getWebsites() as $website) {
            foreach ($website->getGroups() as $group) {
                $stores = $group->getStores();

                foreach ($stores as $store) {
                    $websites[$store->getId()] = $website->getId();
                }
            }
        }

        $this->setOrigData('website_ids', $websites);

        return $this;
    }

    /**
     * @return $this
     */
    public function beforeSave()
    {
        $this->_setWebsiteIds();

        return parent::beforeSave();
    }

    /**
     * @return $this
     */
    public function beforeDelete()
    {
        $this->_setWebsiteIds();

        return parent::beforeDelete();
    }

    /**
     * @return $this
     */
    public function activate()
    {
        $this->setIsActive(1);
        $this->_resource->save($this);

        return $this;
    }

    /**
     * @return $this
     */
    public function inactivate()
    {
        $this->setIsActive(0);
        $this->_resource->save($this);

        return $this;
    }

    /**
     * @param \Magento\Framework\DataObject $object
     * @param $items
     * @return bool
     */
    public function validate(\Magento\Framework\DataObject $object, $items = null)
    {
        if ($items && !$this->backorderValidator->validate($this, $items)) {
            return false;
        }

        if ($object instanceof \Magento\Quote\Model\Quote\Address) {
            $object = $this->subtotalModifier->modify($object);
        }

        return parent::validate($object);
    }
}
