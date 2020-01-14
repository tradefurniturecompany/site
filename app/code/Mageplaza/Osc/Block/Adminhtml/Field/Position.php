<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Osc
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Osc\Block\Adminhtml\Field;

use Magento\Backend\Block\Widget\Container;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Phrase;
use Mageplaza\Osc\Helper\Data as OscHelper;

/**
 * Class Position
 * @package Mageplaza\Osc\Block\Adminhtml\Field
 */
class Position extends Container
{
    /**
     * @var OscHelper
     */
    protected $_oscHelper;

    /**
     * @type array
     */
    protected $sortedFields = [];

    /**
     * @type array
     */
    protected $availableFields = [];

    /**
     * Position constructor.
     *
     * @param Context $context
     * @param OscHelper $oscHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        OscHelper $oscHelper,
        array $data = []
    ) {
        $this->_oscHelper = $oscHelper;

        parent::__construct($context, $data);
    }

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        parent::_construct();

        $this->addButton(
            'save',
            [
                'label'   => __('Save Position'),
                'class'   => 'save primary',
                'onclick' => 'saveOscPosition()'
            ],
            1
        );

        /** Prepare collection */
        list($this->sortedFields, $this->availableFields) = $this->getHelperData()->getAddressHelper()->getSortedField(false);
    }

    /**
     * Retrieve the header text
     *
     * @return Phrase|string
     */
    public function getHeaderText()
    {
        return __('Manage Fields');
    }

    /**
     * @return array
     */
    public function getSortedFields()
    {
        return $this->sortedFields;
    }

    /**
     * @return mixed
     */
    public function getAvailableFields()
    {
        return $this->availableFields;
    }

    /**
     * @return OscHelper
     */
    public function getHelperData()
    {
        return $this->_oscHelper;
    }

    /**
     * @return string
     */
    public function getAjaxUrl()
    {
        return $this->getUrl('*/*/save');
    }
}
