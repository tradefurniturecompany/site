<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CommonRules
 */


namespace Amasty\CommonRules\Model\Rule\Condition\Total;

use Magento\Rule\Model\Condition\Context;

class Status extends \Magento\Rule\Model\Condition\AbstractCondition
{
    /**
     * @var \Magento\Sales\Model\Order\Status
     */
    private $status;

    /**
     * Status constructor.
     * @param Context $context
     * @param \Magento\Sales\Model\Order\Status $status
     * @param array $data
     */
    public function __construct(
        Context $context,
        \Magento\Sales\Model\Order\Status $status,
        array $data = []
    ) {
        $this->setType(\Amasty\CommonRules\Model\Rule\Condition\Total\Status::class)
            ->setValue(null);
        $this->status = $status;
        parent::__construct($context, $data);
    }

    /**
     * @return $this
     */
    public function loadAttributeOptions()
    {
        $statuses = $this->status->getResourceCollection()->getData();
        $options  = $this->getAttributeOptions();

        foreach ($statuses as $status) {
            $options[$status['status']] = $status['label'];
        }

        $this->setAttributeOption($options);

        return $this;
    }

    /**
     * @return $this
     */
    public function loadOperatorOptions()
    {
        $this->setOperatorOption(
            [
                '='  => __('is'),
                '<>' => __('is not'),
            ]
        );

        return $this;
    }

    /**
     * @return string
     */
    public function asHtml()
    {
        $html = $this->getTypeElement()->getHtml() .
            __(
                sprintf(
                    "Order Status %s %s",
                    $this->getOperatorElement()->getHtml(),
                    $this->getAttributeElement()->getHtml()
                )
            );

        if ($this->getId() != '1') {
            $html .= $this->getRemoveLinkHtml();
        }

        return $html;
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $model
     * @return array
     */
    public function validate(\Magento\Framework\Model\AbstractModel $model)
    {
        $result = ['status' => $this->getOperatorForValidate() . "'" . $this->getAttributeElement()->getValue() . "'"];

        return $result;
    }

}

