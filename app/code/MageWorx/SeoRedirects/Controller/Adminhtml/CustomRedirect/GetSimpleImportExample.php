<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoRedirects\Controller\Adminhtml\CustomRedirect;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Component\ComponentRegistrar;

class GetSimpleImportExample extends \Magento\Backend\App\Action
{
    /**
     * @var ComponentRegistrar
     */
    protected $componentRegistrar;

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $fileFactory;

    /**
     * ExportExamplePost constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param ComponentRegistrar $componentRegistrar
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        ComponentRegistrar $componentRegistrar
    ) {
        parent::__construct($context);
        $this->componentRegistrar = $componentRegistrar;
        $this->fileFactory        = $fileFactory;
    }

    /**
     * @return ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Exception
     */
    public function execute()
    {
        $relativeFilePath = implode(
            DIRECTORY_SEPARATOR,
            [
                'examples',
                'example_for_simple_import.csv'
            ]
        );

        $path = $this->componentRegistrar->getPath(
            ComponentRegistrar::MODULE,
            'MageWorx_SeoRedirects'
        );

        $file = $path . DIRECTORY_SEPARATOR . $relativeFilePath;

        $content = file_get_contents($file);

        return $this->fileFactory->create(
            'seoredirects_simple_format_example.csv',
            $content,
            DirectoryList::VAR_DIR
        );
    }

    /**
     * Is access to section allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('MageWorx_SeoRedirects::import_export');
    }
}
