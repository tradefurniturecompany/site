<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoBase\Controller\Adminhtml\Customcanonical;

use MageWorx\SeoBase\Model\ResourceModel\CustomCanonical\CollectionFactory;
use MageWorx\SeoBase\Model\ResourceModel\CustomCanonical\Collection as CustomCanonicalCollection;
use MageWorx\SeoBase\Api\CustomCanonicalRepositoryInterface;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Controller\Result\Redirect as ResultRedirect;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Backend\App\Action\Context;
use Magento\Backend\App\Action;
use Psr\Log\LoggerInterface;

abstract class AbstractMassAction extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'MageWorx_SeoBase::manage_canonical_urls';

    /**
     * @var string
     */
    protected $redirectUrl = '*/*/index';

    /**
     * @var string
     */
    protected $errorMessage = 'Mass Action failed';

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var CustomCanonicalRepositoryInterface
     */
    protected $customCanonicalRepository;

    /**
     * AbstractMassAction constructor.
     *
     * @param Context $context
     * @param Filter $filter
     * @param LoggerInterface $logger
     * @param CollectionFactory $collectionFactory
     * @param CustomCanonicalRepositoryInterface $customCanonicalRepository
     */
    public function __construct(
        Context $context,
        Filter $filter,
        LoggerInterface $logger,
        CollectionFactory $collectionFactory,
        CustomCanonicalRepositoryInterface $customCanonicalRepository
    ) {
        parent::__construct($context);
        $this->filter                    = $filter;
        $this->logger                    = $logger;
        $this->collectionFactory         = $collectionFactory;
        $this->customCanonicalRepository = $customCanonicalRepository;
    }

    /**
     * @return ResultRedirect
     * @throws NotFoundException
     */
    public function execute()
    {
        if (!$this->getRequest()->isPost()) {
            throw new NotFoundException(__('Page not found'));
        }

        try {
            /** @var CustomCanonicalCollection $collection */
            $collection = $this->filter->getCollection($this->collectionFactory->create());

            return $this->massAction($collection);
        } catch (\Exception $e) {
            $resultRedirect = $this->resultRedirectFactory->create();

            $this->messageManager->addErrorMessage($this->errorMessage);
            $this->logger->critical($e);

            return $resultRedirect->setPath($this->redirectUrl);
        }
    }

    /**
     * Execute action to collection items
     *
     * @param CustomCanonicalCollection $collection
     * @return ResultRedirect
     */
    abstract protected function massAction($collection);
}
