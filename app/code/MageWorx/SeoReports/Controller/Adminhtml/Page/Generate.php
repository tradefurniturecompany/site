<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoReports\Controller\Adminhtml\Page;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Generate extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'MageWorx_SeoReports::pages';

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
    protected $generatorKey = 'cms_page';

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
     * Generate CMS Page SEO report
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        try {
            $this->generator = $this->generatorFactory->create($this->generatorKey);
            $this->generator->generate();
            $this->messageManager->addSuccessMessage(__('CMS Page SEO Report was successfully generated'));
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage(
                $e,
                __('Something went wrong while generating the CMS Page SEO Report.')
            );
        }

        $resultRedirect->setPath('mageworx_seoreports/*/index');

        return $resultRedirect;
    }
}
