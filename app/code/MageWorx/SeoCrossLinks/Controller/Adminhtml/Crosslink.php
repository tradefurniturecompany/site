<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoCrossLinks\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\RedirectFactory;
use MageWorx\SeoCrossLinks\Model\CrosslinkFactory;
use Magento\Framework\Registry;

abstract class Crosslink extends Action
{
    /**
     * crosslink factory
     *
     * @var CrosslinkFactory
     */
    protected $crosslinkFactory;

    /**
     * Core registry
     *
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * @var RedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * @param Registry $registry
     * @param CrosslinkFactory $crosslinkFactory
     * @param Context $context
     */
    public function __construct(
        Registry $registry,
        CrosslinkFactory $crosslinkFactory,
        Context $context
    ) {

        $this->coreRegistry = $registry;
        $this->crosslinkFactory = $crosslinkFactory;
        $this->resultRedirectFactory = $context->getResultRedirectFactory();
        parent::__construct($context);
    }

    /**
     * @return \MageWorx\SeoCrossLinks\Model\Crosslink
     */
    protected function initCrosslink($id = null)
    {
        $crosslinkId = $id ? $id : $this->getRequest()->getParam('crosslink_id');

        $crosslink   = $this->crosslinkFactory->create();
        if ($crosslinkId) {
            $crosslink->load($crosslinkId);
        }

        $this->coreRegistry->register('mageworx_seocrosslinks_crosslink', $crosslink);
        return $crosslink;
    }

    /**
     * filter dates
     *
     * @param array $data
     * @return array
     */
    public function filterData($data)
    {
        return $data;
    }

    /**
     * Is access to section allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('MageWorx_SeoCrossLinks::crosslinks');
    }
}
