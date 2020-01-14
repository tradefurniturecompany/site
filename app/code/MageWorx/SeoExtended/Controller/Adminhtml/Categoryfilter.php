<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoExtended\Controller\Adminhtml;

abstract class Categoryfilter extends \Magento\Backend\App\Action
{
    /**
     * @var string
     */
    const ACTION_RESOURCE = 'MageWorx_SeoExtended::categoryfilters';

    /**
     * Core registry
     *
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * Categoryfilter constructor.
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->coreRegistry = $registry;
        parent::__construct($context);
    }

    /**
     *
     * @param array $data
     * @return array
     */
    public function prepareData($data)
    {
        if (isset($data['store_id']) && is_array($data['store_id'])) {
            $data['store_id'] = array_shift($data['store_id']);
        }

        return $data;
    }

    /**
     * is action allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('MageWorx_SeoExtended::categoryfilters');
    }
}
