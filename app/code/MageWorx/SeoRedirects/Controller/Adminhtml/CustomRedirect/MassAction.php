<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoRedirects\Controller\Adminhtml\CustomRedirect;

use Magento\Framework\Exception\LocalizedException;
use MageWorx\SeoRedirects\Controller\Adminhtml\CustomRedirect as CustomRedirectController;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Ui\Component\MassAction\Filter;
use MageWorx\SeoRedirects\Model\ResourceModel\Redirect\CustomRedirect\CollectionFactory;
use MageWorx\SeoRedirects\Model\Redirect\CustomRedirect as CustomRedirectModel;
use MageWorx\SeoRedirects\Model\Redirect\CustomRedirectRepository;

abstract class MassAction extends CustomRedirectController
{
    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var string
     */
    protected $successMessage = 'Mass Action successful on %1 records';
    /**
     * @var string
     */
    protected $errorMessage = 'Mass Action failed';

    /**
     * MassAction constructor.
     *
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param Registry $registry
     * @param Context $context
     */
    public function __construct(
        Filter $filter,
        CollectionFactory $collectionFactory,
        Registry $registry,
        CustomRedirectRepository $customRedirectRepository,
        Context $context
    ) {
        $this->filter                   = $filter;
        $this->collectionFactory        = $collectionFactory;
        $this->customRedirectRepository = $customRedirectRepository;
        parent::__construct($registry, $customRedirectRepository, $context);
    }

    /**
     * @param CustomRedirectModel $redirect
     * @return mixed
     */
    abstract protected function executeAction(CustomRedirectModel $redirect);

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
            foreach ($collection as $redirect) {
                $this->executeAction($redirect);
            }
            $this->messageManager->addSuccessMessage(__($this->successMessage, $collectionSize));
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __($this->errorMessage));
        }
        $redirectResult = $this->resultRedirectFactory->create();
        $redirectResult->setPath('mageworx_seoredirects/*/index');

        return $redirectResult;
    }
}
