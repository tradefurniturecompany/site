<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoAll\Block\Adminhtml\LandingPage\LandingPageGrid;

/**
 * Class Attribute
 */
class AttributeValue extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Text
{
    /**
     * @var DataProvider
     */
    protected $dataProvider;

    /**
     * AttributeValue constructor.
     *
     * @param DataProvider $dataProvider
     * @param \Magento\Backend\Block\Context $context
     * @param array $data
     */
    public function __construct(
        DataProvider $dataProvider,
        \Magento\Backend\Block\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->dataProvider = $dataProvider;
    }

    /**
     * @param \Magento\Framework\DataObject $row
     * @return mixed|string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        parent::render($row);

        return $this->dataProvider->getOptionLabel($row->getAttributeId(), $row->getOptionId());
    }
}
