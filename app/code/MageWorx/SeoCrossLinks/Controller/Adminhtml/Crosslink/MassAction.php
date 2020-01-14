<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoCrossLinks\Controller\Adminhtml\Crosslink;

use Magento\Framework\Exception\LocalizedException;
use MageWorx\SeoCrossLinks\Controller\Adminhtml\Crosslink as CrosslinkController;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use MageWorx\SeoCrossLinks\Model\CrosslinkFactory;
use Magento\Framework\Registry;
use Magento\Ui\Component\MassAction\Filter;
use MageWorx\SeoCrossLinks\Model\ResourceModel\Crosslink\CollectionFactory;
use MageWorx\SeoCrossLinks\Model\Crosslink as CrosslinkModel;

abstract class MassAction extends CrosslinkController
{
    protected $filter;
    protected $collectionFactory;
    /**
     * @var string
     */
    protected $successMessage = 'Mass Action successful on %1 records';
    /**
     * @var string
     */
    protected $errorMessage = 'Mass Action failed';

    public function __construct(
        Filter $filter,
        CollectionFactory $collectionFactory,
        Registry $registry,
        CrosslinkFactory $crosslinkFactory,
        Context $context
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($registry, $crosslinkFactory, $context);
    }

    /**
     * @param CrosslinkModel $crosslink
     * @return mixed
     */
    abstract protected function executeAction(CrosslinkModel $crosslink);

    /**
     * execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            $collectionSize = $collection->getSize();
            foreach ($collection as $crosslinks) {
                $this->executeAction($crosslinks);
            }
            $this->messageManager->addSuccess(__($this->successMessage, $collectionSize));
        } catch (LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __($this->errorMessage));
        }
        $redirectResult = $this->resultRedirectFactory->create();
        $redirectResult->setPath('mageworx_seocrosslinks/*/index');
        return $redirectResult;
    }
}
