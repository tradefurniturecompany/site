<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoRedirects\Controller\Adminhtml\DpRedirect;

use Magento\Framework\Exception\LocalizedException;
use MageWorx\SeoRedirects\Controller\Adminhtml\DpRedirect as DpRedirectController;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use MageWorx\SeoRedirects\Model\Redirect\DpRedirectFactory;
use Magento\Framework\Registry;
use Magento\Ui\Component\MassAction\Filter;
use MageWorx\SeoRedirects\Model\ResourceModel\Redirect\DpRedirect\CollectionFactory;
use MageWorx\SeoRedirects\Model\Redirect\DpRedirect as DpRedirectModel;

abstract class MassAction extends DpRedirectController
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
        DpRedirectFactory $DpRedirectFactory,
        Context $context
    ) {
        $this->filter            = $filter;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($registry, $DpRedirectFactory, $context);
    }

    /**
     * @param DpRedirectModel $redirect
     * @return mixed
     */
    abstract protected function executeAction(DpRedirectModel $redirect);

    /**
     * execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        try {
            $collection     = $this->filter->getCollection($this->collectionFactory->create());
            $collectionSize = $collection->getSize();
            foreach ($collection as $redirects) {
                $this->executeAction($redirects);
            }
            $this->messageManager->addSuccess(__($this->successMessage, $collectionSize));
        } catch (LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __($this->errorMessage));
        }
        $redirectResult = $this->resultRedirectFactory->create();
        $redirectResult->setPath('mageworx_seoredirects/*/index');

        return $redirectResult;
    }
}
