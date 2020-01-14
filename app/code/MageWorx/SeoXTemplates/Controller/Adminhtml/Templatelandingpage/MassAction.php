<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Controller\Adminhtml\Templatelandingpage;

use Magento\Framework\Exception\LocalizedException;
use MageWorx\SeoXTemplates\Controller\Adminhtml\Templatelandingpage;
use Magento\Backend\App\Action\Context;
use MageWorx\SeoXTemplates\Model\Template\LandingPageFactory as TLandingPageFactoryFactory;
use Magento\Framework\Registry;
use Magento\Ui\Component\MassAction\Filter;
use MageWorx\SeoXTemplates\Model\ResourceModel\Template\LandingPage\CollectionFactory;
use MageWorx\SeoXTemplates\Model\Template\LandingPage as TemplateLandingPageModel;

abstract class MassAction extends Templatelandingpage
{
    /**
     *
     * @var \Magento\Ui\Component\MassAction\Filter
     */
    protected $filter;

    /**
     *
     * @var \MageWorx\SeoXTemplates\Model\ResourceModel\Template\LandingPage\CollectionFactory
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
     *
     * @param CollectionFactory $collectionFactory
     * @param Filter $filter
     * @param Registry $registry
     * @param TLandingPageFactoryFactory $templateLandongPageFactory
     * @param Context $context
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        Filter $filter,
        Registry $registry,
        TLandingPageFactoryFactory $templateLandongPageFactory,
        Context $context
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->filter = $filter;
        parent::__construct($registry, $templateLandongPageFactory, $context);
    }

    /**
     *
     * @param TemplateLandingPageModel $template
     * @return mixed
     */
    abstract protected function doTheAction(TemplateLandingPageModel $template);

    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            $collectionSize = $collection->getSize();
            foreach ($collection as $template) {
                $this->doTheAction($template);
            }
            $this->messageManager->addSuccess(__($this->successMessage, $collectionSize));
        } catch (LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __($this->errorMessage));
        }
        $redirectResult = $this->resultRedirectFactory->create();
        $redirectResult->setPath('mageworx_seoxtemplates/*/index');
        return $redirectResult;
    }
}
