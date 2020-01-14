<?php

/**
 * Copyright © 2019 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoAll\Block\Adminhtml\System\Config;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\App\ProductMetadataInterface;

class HideForMagentoEE extends Field
{
    /**
     * @var ProductMetadataInterface
     */
    protected $productMetadata;

    /**
     * HideForMagentoEE constructor.
     *
     * @param Context $context
     * @param ProductMetadataInterface $productMetadata
     * @param array $data
     */
    public function __construct(
        Context $context,
        ProductMetadataInterface $productMetadata,
        array $data = []
    ) {
        $this->productMetadata = $productMetadata;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve HTML markup for given form element
     *
     * @param AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        if ($this->productMetadata->getEdition() == 'Enterprise') {
            return '';
        }

        return parent::render($element);
    }
}
