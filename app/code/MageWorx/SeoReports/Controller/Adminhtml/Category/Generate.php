<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoReports\Controller\Adminhtml\Category;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Generate extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'MageWorx_SeoReports::categories';

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \MageWorx\SeoReports\Model\GeneratorFactory
     */
    protected $generatorFactory;

    /**
     * @var \MageWorx\SeoReports\Model\Generator\AbstractReportGenerator
     */
    protected $generator;

    /**
     * @var string
     */
    protected $generatorKey = 'catalog_category';

    /**
     * Generate constructor.
     *
     * @param \MageWorx\SeoReports\Model\GeneratorFactory $generatorFactory
     * @param Context $context
     */
    public function __construct(
        \MageWorx\SeoReports\Model\GeneratorFactory $generatorFactory,
        Context $context
    ) {
        $this->generatorFactory = $generatorFactory;
        parent::__construct($context);
    }

    /**
     * Generate category SEO report
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        try {
            $this->generator = $this->generatorFactory->create($this->generatorKey);
            $this->generator->generate();
            $this->messageManager->addSuccessMessage(__('Category SEO report was successfully generated'));
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage(
                $e,
                __('Something went wrong while generating the Category SEO Report.')
            );
        }

        $resultRedirect->setPath('mageworx_seoreports/*/index');

        return $resultRedirect;
    }
}
