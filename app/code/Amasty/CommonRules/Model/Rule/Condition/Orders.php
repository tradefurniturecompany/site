<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CommonRules
 */


namespace Amasty\CommonRules\Model\Rule\Condition;

use Magento\Framework\App\ResourceConnection as AppResource;
use Magento\Rule\Model\Condition as Condition;

class Orders extends \Magento\Rule\Model\Condition\AbstractCondition
{
    /**
     * @var Resource
     */
    private $resource;

    /**
     * Orders constructor.
     * @param Condition\Context $context
     * @param AppResource $resource
     * @param array $data
     */
    public function __construct(
        Condition\Context $context,
        AppResource $resource,
        array $data = []
    ) {
        $this->resource = $resource;
        parent::__construct($context, $data);
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getConditionLabel()
    {
        return __('Purchases history');
    }

    /**
     * @return $this
     */
    public function loadAttributeOptions()
    {
        $attributes = array(
            'order_num'    => __('Number of Completed Orders'),
            'sales_amount' => __('Total Sales Amount'),
        );

        $this->setAttributeOption($attributes);

        return $this;
    }

    /**
     * @return $this
     */
    public function getAttributeElement()
    {
        $element = parent::getAttributeElement();
        $element->setShowAsText(true);

        return $element;
    }

    /**
     * @return string
     */
    public function getInputType()
    {
        return 'numeric';
    }

    /**
     * @return string
     */
    public function getValueElementType()
    {
        return 'text';
    }

    /**
     * @return mixed
     */
    public function getValueSelectOptions()
    {
        $options = array();

        $key = 'value_select_options';
        if (!$this->hasData($key)) {
            $this->setData($key, $options);
        }

        return $this->getData($key);
    }

    /**
     * Validate Address Rule Condition
     *
     * @param \Magento\Framework\Model\AbstractModel $model
     * @return bool
     */
    public function validate(\Magento\Framework\Model\AbstractModel $model)
    {
        $quote = $model;

        if (!$quote instanceof \Magento\Quote\Model\Quote) {
            $quote = $model->getQuote();
        }

        $num = 0;
        if ($quote->getCustomerId()){
            $db = $this->resource->getConnection('default');

            $select = $db->select()
                ->from(['o' => $this->resource->getTableName('sales_order')], [])
                ->where('o.customer_id = ?', $quote->getCustomerId())
                ->where('o.status = ?', 'complete');

            if ('order_num' == $this->getAttribute()) {
                $select->from(null, [new \Zend_Db_Expr('COUNT(*)')]);
            } elseif ('sales_amount' == $this->getAttribute()){
                $select->from(null, [new \Zend_Db_Expr('SUM(o.base_grand_total)')]);
            }

            $num = $db->fetchOne($select);
        }

        return $this->validateAttribute($num);
    }
}
