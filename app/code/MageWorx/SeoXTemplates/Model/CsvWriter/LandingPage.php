<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoXTemplates\Model\CsvWriter;

use Magento\Framework\Filesystem;
use MageWorx\SeoXTemplates\Model\DataProviderLandingPageFactory;

class LandingPage extends \MageWorx\SeoXTemplates\Model\CsvWriter
{
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var DataProviderLandingPageFactory
     */
    protected $dataProviderLandingPageFactory;

    /**
     * LandingPage constructor.
     *
     * @param \Magento\Store\Model\StoreManagerInterface $_storeManager
     * @param Filesystem $fileSystem
     * @param DataProviderLandingPageFactory $dataProviderLandingPageFactory
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $_storeManager,
        Filesystem $fileSystem,
        DataProviderLandingPageFactory $dataProviderLandingPageFactory
    ) {
        $this->_storeManager = $_storeManager;
        $this->dataProviderLandingPageFactory = $dataProviderLandingPageFactory;
        parent::__construct($fileSystem);
    }

    /**
     * Write to CSV file converted string from template code and retrive file params
     *
     * @param \MageWorx\SeoXTemplates\Model\ResourceModel\Template\LandingPage\Collection $collection
     * @param \MageWorx\SeoXTemplates\Model\AbstractTemplate $template
     * @param string|null $filenameParam
     * @param int|null $nestedStoreId
     * @return array
     */
    public function write($collection, $template, $filenameParam = null, $nestedStoreId = null)
    {
        if (!$collection->count()) {
            return false;
        }

        $dataProvider = $this->dataProviderLandingPageFactory->create($template->getTypeId());

        $data = $dataProvider->getData($collection, $template, $nestedStoreId);
        $filename = $filenameParam ? $filenameParam : 'export/'. md5(microtime()) . '.csv';

        $stream = $this->directory->openFile($filename, 'a+');
        if (!$filenameParam) {
            $stream->writeCsv($this->_getHeaderData());
        }

        $stream->lock();

        foreach ($data as $landingPageId => $landingPageData) {
            if (empty($landingPageData['value'])) {
                continue;
            }

            $write = $landingPageData;
            $stream->writeCsv($write);
        }

        $stream->unlock();
        $stream->close();

        return [
            'type' => 'filename',
            'value' => $filename,
            'rm' => true  // can delete file after use
        ];
    }

    /**
     * Retrieve header for report
     *
     * @return array
     */
    protected function _getHeaderData()
    {
        return [
            __('Landing Page ID'),
            __('Landing Page Title'),
            __('Store ID'),
            __('Store Name'),
            __('Target'),
            __('Current Store Value'),
            __('New Store Value')
        ];
    }
}
